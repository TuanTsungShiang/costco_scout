<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PriceScrapeController extends Controller
{
    public function scrape(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url|max:1000',
        ]);

        $url = $validated['url'];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
                    'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'zh-TW,zh;q=0.9,en;q=0.8',
                ])
                ->get($url);
        } catch (\Throwable $e) {
            return response()->json(['error' => '無法連線到該網址：' . $e->getMessage()], 502);
        }

        if (!$response->ok()) {
            return response()->json(['error' => "網頁回應 {$response->status()}，無法擷取"], 502);
        }

        $html = $response->body();

        $price      = $this->extractPrice($html);
        $name       = $this->extractName($html);
        $itemNumber = $this->extractItemNumber($url, $html);
        $imageUrl   = $this->extractImage($html);

        // 用 Gemini 把商品名稱拆成品牌 + 規格（best-effort，失敗不影響擷取）
        $specs = $name ? $this->parseNameWithGemini($name) : null;

        if ($price === null) {
            return response()->json(array_merge([
                'error'        => '這個頁面找不到價格（可能是需要登入、缺貨、或改版）。請手動輸入。',
                'name'         => $name,
                'item_number'  => $itemNumber,
                'source_url'   => $url,
            ], $specs ?? []), 422);
        }

        return response()->json(array_merge([
            'price_twd'    => $price,
            'name'         => $name,
            'item_number'  => $itemNumber,
            'image_url'    => $imageUrl,
            'source_url'   => $url,
        ], $specs ?? []));
    }

    /**
     * 用 Gemini 把商品名稱字串拆成品牌 + 規格。
     * 回傳含 brand / name / comparison_mode / package_count / content_per_package /
     * content_unit / comparison_quantity / comparison_unit 的陣列；失敗回 null。
     * 共用 config('services.gemini.models') 的 fallback 清單。
     */
    private function parseNameWithGemini(string $productName): ?array
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return null;
        }
        $models = config('services.gemini.models', []);

        $prompt = implode("\n", [
            '以下是台灣 Costco 商品名稱，請拆解成結構化 JSON：',
            "「{$productName}」",
            '',
            '{"brand":"","name":"","comparison_mode":"","package_count":null,"content_per_package":null,"content_unit":"","comparison_quantity":100,"comparison_unit":""}',
            '',
            '規則：',
            '- brand：品牌（如 Red Dragon、Kirkland Signature、雀巢）',
            '- name：品名（去掉品牌與規格數字，例如「冷凍焗烤牛肉派」）',
            '- package_count：包裝件數（如 6 Count / 6入 填 6；單件填 1）',
            '- content_per_package：每件容量數字（220g 填 220；900毫升填 900；1.36kg 填 1360）',
            '- content_unit：ML / G / SHEET / COUNT 其中一個',
            '- comparison_mode：VOLUME / WEIGHT / SHEET / COUNT / BUNDLE 其中一個',
            '- comparison_quantity：容量/重量商品填 100；純件數商品填 1',
            '- comparison_unit：ml / g / 張 / 個 / 粒 等',
            '',
            '只回傳 JSON，不要其他文字。',
        ]);

        // 逐一嘗試 model：遇到 HTTP 錯誤 or 壞 JSON 都換下一個重試，直到成功。
        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            try {
                $resp = Http::timeout(20)->post($url, [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature'      => 0,
                        // Gemini 3.x 的 thinking 會先吃掉額度，需留足空間避免 JSON 被截斷
                        'maxOutputTokens'  => 2048,
                        'responseMimeType' => 'application/json',
                    ],
                ]);
            } catch (\Throwable $e) {
                continue; // 連線失敗 → 換下一個 model
            }

            if (!$resp->ok()) {
                continue; // 429/404/503/其他 → 換下一個 model
            }

            // thinking 模型可能把輸出拆成多個 part，全部串起來
            $parts = $resp->json('candidates.0.content.parts', []);
            $text  = '';
            foreach ($parts as $part) {
                $text .= $part['text'] ?? '';
            }
            $text   = preg_replace('/^```[a-z]*\s*|\s*```$/m', '', trim($text));
            $parsed = json_decode($text, true);

            if (!is_array($parsed) || empty($parsed['brand']) && empty($parsed['comparison_mode'])) {
                continue; // JSON 壞掉或內容空 → 換下一個 model 重試
            }

            // 只保留有值的欄位，避免空字串覆蓋擷取到的原始資料
            return array_filter([
                'brand'               => $parsed['brand']               ?? null,
                'name'                => !empty($parsed['name']) ? $parsed['name'] : null,
                'comparison_mode'     => $parsed['comparison_mode']     ?? null,
                'package_count'       => isset($parsed['package_count'])       ? (int) $parsed['package_count']       : null,
                'content_per_package' => isset($parsed['content_per_package']) ? (int) $parsed['content_per_package'] : null,
                'content_unit'        => $parsed['content_unit']        ?? null,
                'comparison_quantity' => isset($parsed['comparison_quantity']) ? (int) $parsed['comparison_quantity'] : null,
                'comparison_unit'     => $parsed['comparison_unit']     ?? null,
            ], fn ($v) => $v !== null && $v !== '');
        }

        return null; // 所有 model 都失敗
    }

    /**
     * 價格擷取優先序：
     * 1. <meta property="product:price:amount"> / og:price:amount
     * 2. JSON-LD product 區塊的 "price"
     */
    private function extractPrice(string $html): ?int
    {
        foreach (['product:price:amount', 'og:price:amount'] as $prop) {
            if (preg_match('/<meta[^>]*(?:property|name)=["\']' . preg_quote($prop, '/') . '["\'][^>]*content=["\']([\d.,]+)["\']/i', $html, $m)) {
                $val = (float) str_replace(',', '', $m[1]);
                if ($val > 0) {
                    return (int) round($val);
                }
            }
        }

        // Fallback：JSON-LD product 區塊裡第一個 "price"
        if (preg_match('/"@type"\s*:\s*"product".*?"price"\s*:\s*"?([\d.,]+)"?/is', $html, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            if ($val > 0) {
                return (int) round($val);
            }
        }

        return null;
    }

    /** 商品名稱：JSON-LD product name 優先，其次 og:title（去掉 " | Costco..." 尾綴） */
    private function extractName(string $html): ?string
    {
        if (preg_match('/"@type"\s*:\s*"product".*?"name"\s*:\s*"([^"]+)"/is', $html, $m)) {
            return $this->cleanName($m[1]);
        }

        if (preg_match('/<meta[^>]*property=["\']og:title["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m)) {
            return $this->cleanName($m[1]);
        }

        return null;
    }

    private function cleanName(string $name): string
    {
        $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // 去掉 " | Costco 好市多" 類的網站名尾綴
        $name = preg_replace('/\s*[|｜]\s*Costco.*$/iu', '', $name);
        return trim($name);
    }

    /** 商品編號：優先 costco URL 的 /p/{id}，其次 JSON-LD sku/productID */
    private function extractItemNumber(string $url, string $html): ?string
    {
        if (preg_match('#/p/(\d{4,10})#', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/"(?:sku|productID|mpn)"\s*:\s*"?(\d{4,10})"?/i', $html, $m)) {
            return $m[1];
        }
        return null;
    }

    private function extractImage(string $html): ?string
    {
        if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m)) {
            return $m[1];
        }
        return null;
    }
}
