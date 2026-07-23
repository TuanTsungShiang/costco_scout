<?php

namespace Database\Seeders;

use App\Enums\ComparisonMode;
use App\Enums\ContentUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ── 你剛剛拍的那瓶 ──────────────────────────────────
            [
                'brand'              => '雀巢 NESCAFÉ',
                'name'               => 'EXCELLA 無糖黑咖啡',
                'comparison_mode'    => ComparisonMode::VOLUME->value,
                'package_count'      => 12,
                'content_per_package'=> 900,     // 900ml / 瓶
                'content_unit'       => ContentUnit::ML->value,
                'comparison_quantity'=> 100,
                'comparison_unit'    => 'ml',
                'notes'              => '品號 #161342。900毫升×12入，每100ml比價。',
            ],

            // ── 常見好市多商品 ──────────────────────────────────
            [
                'brand'              => 'Kirkland Signature',
                'name'               => '科克蘭 杏仁果',
                'comparison_mode'    => ComparisonMode::WEIGHT->value,
                'package_count'      => 1,
                'content_per_package'=> 1360,    // 1360g
                'content_unit'       => ContentUnit::G->value,
                'comparison_quantity'=> 100,
                'comparison_unit'    => 'g',
                'notes'              => '烘焙杏仁果 1.36kg，每100g比價。',
            ],
            [
                'brand'              => 'Kirkland Signature',
                'name'               => '科克蘭 有機藜麥',
                'comparison_mode'    => ComparisonMode::WEIGHT->value,
                'package_count'      => 2,
                'content_per_package'=> 907,     // 907g / 包
                'content_unit'       => ContentUnit::G->value,
                'comparison_quantity'=> 100,
                'comparison_unit'    => 'g',
                'notes'              => '有機藜麥 907g×2包，每100g比價。',
            ],
            [
                'brand'              => 'Kirkland Signature',
                'name'               => '科克蘭 衛生紙',
                'comparison_mode'    => ComparisonMode::SHEET->value,
                'package_count'      => 30,
                'content_per_package'=> 300,     // 300張 / 捲
                'content_unit'       => ContentUnit::SHEET->value,
                'comparison_quantity'=> 100,
                'comparison_unit'    => '張',
                'notes'              => '三層衛生紙 30捲×300張，每100張比價。',
            ],
            [
                'brand'              => 'Kirkland Signature',
                'name'               => '科克蘭 橄欖油',
                'comparison_mode'    => ComparisonMode::VOLUME->value,
                'package_count'      => 2,
                'content_per_package'=> 3000,    // 3L / 瓶
                'content_unit'       => ContentUnit::ML->value,
                'comparison_quantity'=> 100,
                'comparison_unit'    => 'ml',
                'notes'              => 'Extra Virgin 橄欖油 3L×2瓶，每100ml比價。',
            ],
        ];

        foreach ($products as $data) {
            DB::table('canonical_products')->updateOrInsert(
                ['brand' => $data['brand'], 'name' => $data['name']],
                array_merge($data, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✓ ProductSeeder: ' . count($products) . ' 個商品已建立');
    }
}
