<?php

return [
    'name' => env('APP_NAME', 'BetWinner Admin & API'),
    'currency' => env('APP_CURRENCY', 'Tshs'),
    'theme' => [
        'primary' => '#013220', // Dark Forest Green
        'secondary' => '#39FF88', // Electric Green
        'gold' => '#DDD06A', // Gold
        'card' => '#0B3B24',
        'background' => '#013220',
    ],
    'roles' => [
        'super_admin' => 'Super Administrator',
        'tipster' => 'Verified Tipster',
        'user' => 'Mobile Bettor',
    ],
    'bookmakers' => [
        'Betika' => [
            'slug' => 'betika',
            'name' => 'Betika',
            'code_prefix' => 'BTK',
            'color' => '#DDD06A',
            'website' => 'https://www.betika.co.tz',
        ],
        'SportyBet' => [
            'slug' => 'sportybet',
            'name' => 'SportyBet',
            'code_prefix' => 'SB',
            'color' => '#E53E3E',
            'website' => 'https://www.sportybet.com',
        ],
        '1xBet' => [
            'slug' => '1xbet',
            'name' => '1xBet',
            'code_prefix' => '1X',
            'color' => '#1E40AF',
            'website' => 'https://1xbet.co.tz',
        ],
        'MozzartBet' => [
            'slug' => 'mozzartbet',
            'name' => 'MozzartBet',
            'code_prefix' => 'MOZ',
            'color' => '#1E3A8A',
            'website' => 'https://www.mozzartbet.co.tz',
        ],
        'Betway' => [
            'slug' => 'betway',
            'name' => 'Betway',
            'code_prefix' => 'BTW',
            'color' => '#15803D',
            'website' => 'https://www.betway.co.tz',
        ],
    ],
    'vip_plans' => [
        'single' => [
            'name' => 'Single Slip Access',
            'price' => 2000,
            'duration_days' => 1,
            'description' => 'Instant unlock for a single VIP ticket & booking code',
        ],
        'weekly' => [
            'name' => 'Weekly VIP Pass',
            'price' => 7500,
            'duration_days' => 7,
            'description' => 'Unlimited VIP tickets for 7 full days',
        ],
        'monthly' => [
            'name' => 'Monthly VIP Club',
            'price' => 20000,
            'duration_days' => 30,
            'description' => 'Complete VIP access to all tipster bankers & slips for 30 days',
        ],
    ],
];
