<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BookmakerSeeder::class,
            UserAndTipsterSeeder::class,
            BetslipSeeder::class,
            FeaturedMatchSeeder::class,
            HomeAdSeeder::class,
            VipPlanSeeder::class,
        ]);
    }
}
