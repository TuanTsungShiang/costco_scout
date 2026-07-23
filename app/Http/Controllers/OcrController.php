<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OcrController extends Controller
{
    private string $prompt = <<<'PROMPT'
這是台灣好市多（Costco）的賣場價格標籤。請擷取以下欄位，以 JSON 回應：
{"item_number":"","brand":"","name":"","price_twd":0,"package_count":null,"content_per_package":null,"content_unit":"","comparison_mode":"","comparison_quantity":100,"comparison_unit":""}

規則：
- item_number：# 後面的數字（6-9位，只要數字）
- brand：品牌名稱（如「雀巢」「Kirkland Signature」）
- name：商品名稱（不含品牌、不含規格數字）
- price_twd：售價整數（不含 $ 或元）
- package_count：包裝件數（如「12入」填 12，單件填 1）
- content_per_package：每件容量數字（如 900毫升填 900；1.36公斤填 1360）
- content_unit：ML / G / SHEET / COUNT 其中一個
- comparison_mode：VOLUME / WEIGHT / SHEET / COUNT / BUNDLE 其中一個
- comparison_quantity：比較基準量（容量/重量商品填 100；件數商品填 1）
- comparison_unit：ml / g / 張 / 個 等

只回傳 JSON，不要其他文字。
PROMPT;

    public function recognize(Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|string']);

        $dataUrl = $request->input('image');
        if (!str_contains($dataUrl, ',')) {
            return response()->json(['error' => '圖片格式錯誤'], 422);
        }

        [$meta, $base64] = explode(',', $dataUrl, 2);
        preg_match('/data:([^;]+);base64/', $meta, $m);
        $mediaType = $m[1] ?? 'image/jpeg';

        // 優先用 Gemini（有免費額度），fallback 到 Anthropic
        if (config('services.gemini.api_key')) {
            return $this->recognizeWithGemini($base64, $mediaType);
        }

        if (config('services.anthropic.api_key')) {
            return $this->recognizeWithAnthropic($base64, $mediaType);
        }

        return response()->json(['error' => '未設定 GEMINI_API_KEY 或 ANTHROPIC_API_KEY'], 500);
    }

    private function recognizeWithGemini(string $base64, string $mediaType): JsonResponse
    {
        $apiKey = config('services.gemini.api_key');
        $models = config('services.gemini.models', ['gemini-flash-latest']);

        $payload = [
            'contents' => [[
                'parts' => [
                    ['inline_data' => ['mime_type' => $mediaType, 'data' => $base64]],
                    ['text' => $this->prompt],
                ],
            ]],
            'generationConfig' => [
                'temperature'      => 0,
                'maxOutputTokens'  => 2048,
                'responseMimeType' => 'application/json',
            ],
        ];

        $exhausted = []; // 記錄哪些 model 配額用盡，供最終錯誤訊息參考

        foreach ($models as $model) {
            $url      = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            $response = Http::timeout(30)->post($url, $payload);

            // 429 配額用盡 → 換下一個 model
            if ($response->status() === 429) {
                $exhausted[] = $model;
                continue;
            }

            // 404 不存在 / 503 過載 → 換下一個 model
            if (in_array($response->status(), [404, 503], true)) {
                continue;
            }

            if (!$response->ok()) {
                return response()->json([
                    'error'        => "Gemini API 錯誤 {$response->status()}（model={$model}）：" . $response->json('error.message', $response->body()),
                    'raw_response' => $response->body(),
                ], 502);
            }

            $text         = $response->json('candidates.0.content.parts.0.text', '');
            $finishReason = $response->json('candidates.0.finishReason', 'UNKNOWN');

            if ($text === '') {
                return response()->json([
                    'error' => "[gemini:{$model}] 回應為空 (finishReason={$finishReason})。完整回應：" . $response->body(),
                ], 502);
            }

            return $this->parseAndReturn($text, "gemini:{$model}", $finishReason);
        }

        // 所有 model 都配額用盡
        return response()->json([
            'error' => '所有 Gemini model 今日免費額度都用完了（每個 model 每天 20 次）。'
                     . '用盡清單：' . implode('、', $exhausted)
                     . '。請明天再試，或在 config/services.php 的 gemini.models 加入更多 model。',
        ], 429);
    }

    private function recognizeWithAnthropic(string $base64, string $mediaType): JsonResponse
    {
        $apiKey = config('services.anthropic.api_key');

        $response = Http::timeout(30)->withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 512,
            'messages'   => [[
                'role'    => 'user',
                'content' => [
                    [
                        'type'   => 'image',
                        'source' => [
                            'type'       => 'base64',
                            'media_type' => $mediaType,
                            'data'       => $base64,
                        ],
                    ],
                    ['type' => 'text', 'text' => $this->prompt],
                ],
            ]],
        ]);

        if (!$response->ok()) {
            return response()->json([
                'error' => 'Claude API 錯誤 ' . $response->status() . '：' . $response->json('error.message', $response->body()),
            ], 502);
        }

        $text = $response->json('content.0.text', '{}');

        return $this->parseAndReturn($text, 'anthropic');
    }

    private function parseAndReturn(string $raw, string $provider, string $finishReason = ''): JsonResponse
    {
        // Strip markdown fences
        $text   = preg_replace('/^```[a-z]*\s*|\s*```$/m', '', trim($raw));
        $parsed = json_decode($text, true);

        if (!is_array($parsed)) {
            $reasonHint = $finishReason === 'MAX_TOKENS'
                ? '（回應被 token 上限截斷，請提高 maxOutputTokens 或關閉 thinking）'
                : ($finishReason ? "（finishReason={$finishReason}）" : '');
            return response()->json([
                'error'        => "[{$provider}] JSON 解析失敗{$reasonHint}",
                'raw_response' => $raw,
                'finish_reason'=> $finishReason,
            ], 502);
        }

        return response()->json([
            'provider'            => $provider,
            'item_number'         => $parsed['item_number']         ?? null,
            'brand'               => $parsed['brand']               ?? null,
            'name'                => $parsed['name']                ?? null,
            'price_twd'           => isset($parsed['price_twd'])           ? (int) $parsed['price_twd']           : null,
            'package_count'       => isset($parsed['package_count'])       ? (int) $parsed['package_count']       : null,
            'content_per_package' => isset($parsed['content_per_package']) ? (int) $parsed['content_per_package'] : null,
            'content_unit'        => $parsed['content_unit']        ?? null,
            'comparison_mode'     => $parsed['comparison_mode']     ?? null,
            'comparison_quantity' => isset($parsed['comparison_quantity']) ? (int) $parsed['comparison_quantity'] : 100,
            'comparison_unit'     => $parsed['comparison_unit']     ?? null,
        ]);
    }
}
