<?php

namespace Database\Seeders;

use App\Models\Bookmaker;
use Illuminate\Database\Seeder;

class BookmakerSeeder extends Seeder
{
    public function run(): void
    {
        $bookmakers = [
            [
                'name' => 'Betika',
                'slug' => 'betika',
                'code_prefix' => 'BTK',
                'brand_color' => '#DDD06A',
                'website_url' => 'https://www.betika.co.tz',
                'logo_path' => 'logos/betika.png',
                'sort_order' => 1,
            ],
            [
                'name' => 'SportyBet',
                'slug' => 'sportybet',
                'code_prefix' => 'SB',
                'brand_color' => '#E53E3E',
                'website_url' => 'https://www.sportybet.com',
                'logo_path' => 'logos/sportybet.png',
                'sort_order' => 2,
            ],
            [
                'name' => '1xBet',
                'slug' => '1xbet',
                'code_prefix' => '1X',
                'brand_color' => '#1E40AF',
                'website_url' => 'https://1xbet.co.tz',
                'logo_path' => 'logos/1xbet.png',
                'sort_order' => 3,
            ],
            [
                'name' => 'MozzartBet',
                'slug' => 'mozzartbet',
                'code_prefix' => 'MOZ',
                'brand_color' => '#1E3A8A',
                'website_url' => 'https://www.mozzartbet.co.tz',
                'logo_path' => 'logos/mozzartbet.png',
                'sort_order' => 4,
            ],
            [
                'name' => 'Betway',
                'slug' => 'betway',
                'code_prefix' => 'BTW',
                'brand_color' => '#15803D',
                'website_url' => 'https://www.betway.co.tz',
                'logo_path' => 'logos/betway.png',
                'sort_order' => 5,
            ],
        ];

        foreach ($bookmakers as $data) {
            Bookmaker::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
