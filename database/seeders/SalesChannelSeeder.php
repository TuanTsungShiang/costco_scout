<?php

namespace Database\Seeders;

use App\Models\SalesChannel;
use Illuminate\Database\Seeder;

class SalesChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            [
                'name'                             => '蝦皮購物',
                'slug'                             => 'shopee',
                'platform_fee_basis_points'        => 300,  // 3%
                'payment_fee_basis_points'         => 200,  // 2%
                'promotion_fee_basis_points'       => 0,
                'default_shipping_minor'           => 0,    // 蝦皮常有免運
                'default_packaging_minor'          => 30,   // NT$30 包材
                'expected_return_loss_basis_points'=> 100,  // 1%
                'is_active'                        => true,
            ],
            [
                'name'                             => 'momo 購物',
                'slug'                             => 'momo',
                'platform_fee_basis_points'        => 500,  // 5%
                'payment_fee_basis_points'         => 200,  // 2%
                'promotion_fee_basis_points'       => 100,  // 1%
                'default_shipping_minor'           => 0,    // 含運
                'default_packaging_minor'          => 50,
                'expected_return_loss_basis_points'=> 150,  // 1.5%
                'is_active'                        => true,
            ],
            [
                'name'                             => 'PChome 24h',
                'slug'                             => 'pchome',
                'platform_fee_basis_points'        => 500,
                'payment_fee_basis_points'         => 200,
                'promotion_fee_basis_points'       => 0,
                'default_shipping_minor'           => 0,
                'default_packaging_minor'          => 50,
                'expected_return_loss_basis_points'=> 100,
                'is_active'                        => true,
            ],
            [
                'name'                             => 'Yahoo 購物',
                'slug'                             => 'yahoo',
                'platform_fee_basis_points'        => 400,
                'payment_fee_basis_points'         => 200,
                'promotion_fee_basis_points'       => 100,
                'default_shipping_minor'           => 0,
                'default_packaging_minor'          => 50,
                'expected_return_loss_basis_points'=> 100,
                'is_active'                        => true,
            ],
            [
                'name'                             => '露天拍賣',
                'slug'                             => 'ruten',
                'platform_fee_basis_points'        => 300,
                'payment_fee_basis_points'         => 150,
                'promotion_fee_basis_points'       => 0,
                'default_shipping_minor'           => 60,   // NT$60 運費
                'default_packaging_minor'          => 30,
                'expected_return_loss_basis_points'=> 50,
                'is_active'                        => true,
            ],
        ];

        foreach ($channels as $ch) {
            SalesChannel::create($ch);
        }
    }
}
