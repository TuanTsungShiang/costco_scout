<?php

namespace Database\Seeders;

use App\Models\Retailer;
use App\Models\Store;
use Illuminate\Database\Seeder;

class RetailerSeeder extends Seeder
{
    public function run(): void
    {
        $costco = Retailer::create([
            'name'         => 'Costco 好市多',
            'slug'         => 'costco-tw',
            'type'         => 'BOTH',
            'country_code' => 'TW',
            'is_active'    => true,
        ]);

        Store::create([
            'retailer_id'   => $costco->id,
            'branch_name'   => '內湖店',
            'country_code'  => 'TW',
            'currency_code' => 'TWD',
            'timezone'      => 'Asia/Taipei',
            'is_active'     => true,
        ]);

        Store::create([
            'retailer_id'   => $costco->id,
            'branch_name'   => '線上商店',
            'country_code'  => 'TW',
            'currency_code' => 'TWD',
            'timezone'      => 'Asia/Taipei',
            'is_active'     => true,
        ]);

        $retailers = [
            ['name' => 'momo 購物網', 'slug' => 'momo-tw', 'type' => 'ONLINE'],
            ['name' => 'PChome 24h', 'slug' => 'pchome-tw', 'type' => 'ONLINE'],
            ['name' => '蝦皮購物',   'slug' => 'shopee-tw', 'type' => 'ONLINE'],
            ['name' => 'Yahoo 購物', 'slug' => 'yahoo-tw',  'type' => 'ONLINE'],
            ['name' => '露天拍賣',   'slug' => 'ruten-tw',  'type' => 'ONLINE'],
        ];

        foreach ($retailers as $r) {
            Retailer::create([
                'name'         => $r['name'],
                'slug'         => $r['slug'],
                'type'         => $r['type'],
                'country_code' => 'TW',
                'is_active'    => true,
            ]);
        }
    }
}
