<?php

return [
    'base_currency' => env('ARBITRAGE_BASE_CURRENCY', 'TWD'),

    'costco' => [
        'membership_tier' => env('COSTCO_MEMBERSHIP_TIER', 'EXECUTIVE'),
        // Executive reward rate in basis points (null = not configured)
        'executive_reward_rate_basis_points' => env('COSTCO_EXECUTIVE_REWARD_BPS', null),
        // Annual reward cap in minor currency units (null = no cap configured)
        'executive_reward_cap_minor' => env('COSTCO_EXECUTIVE_REWARD_CAP', null),
        // Cobrand card extra reward in basis points
        'cobrand_card_rate_basis_points' => env('COSTCO_COBRAND_CARD_BPS', null),
    ],

    'online' => [
        // Free shipping threshold in minor units, keyed by retailer slug
        'shipping_threshold_minor' => [],
        // Default shipping fee in minor units, keyed by retailer slug
        'default_shipping_fee_minor' => [],
    ],

    'resale' => [
        // Minimum ROI (basis points) to consider TEST_ONE_UNIT
        'min_roi_basis_points' => env('ARBITRAGE_MIN_ROI_BPS', 800),
    ],
];
