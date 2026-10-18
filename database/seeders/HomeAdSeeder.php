<?php

namespace Database\Seeders;

use App\Models\Bookmaker;
use App\Models\HomeAd;
use Illuminate\Database\Seeder;

class HomeAdSeeder extends Seeder
{
    public function run(): void
    {
        $betika = Bookmaker::where('slug', 'betika')->first();
        $sportybet = Bookmaker::where('slug', 'sportybet')->first();
        $mozzart = Bookmaker::where('slug', 'mozzartbet')->first();
        $betway = Bookmaker::where('slug', 'betway')->first();

        $ads = [
            [
                'bookmaker_id' => $betika?->id,
                'badge' => 'BETIKA MIDWEEK JACKPOT',
                'title' => 'Tshs 15,000,000 Super Jackpot',
                'subtitle' => '15 matches analyzed by VIP tipsters with full 1X2 combinations & form guides.',
                'highlight' => 'Tshs 1,000 Stake • 100% Free First Slip',
                'cta_text' => 'View Jackpot Slip',
                'promo_code' => 'JACKPOT15M',
                'sort_order' => 1,
            ],
            [
                'bookmaker_id' => $sportybet?->id,
                'badge' => 'SPORTYBET CASHBACK PROMO',
                'title' => '50% Instant Cashback on All Slips',
                'subtitle' => 'Top up your wallet or purchase any premium tipster slip and receive instant M-Pesa rebate.',
                'highlight' => 'No minimum deposit • Auto-credited',
                'cta_text' => 'Deposit with M-Pesa',
                'promo_code' => 'CASHBACK50',
                'sort_order' => 2,
            ],
            [
                'bookmaker_id' => $mozzart?->id,
                'badge' => 'MOZZARTBET VIP PASS',
                'title' => 'Join Elite 90%+ Win Rate Channel',
                'subtitle' => 'Get daily morning banker slips directly with MozzartBet & Betika booking codes.',
                'highlight' => 'Limited spots • Weekend VIP access',
                'cta_text' => 'Claim VIP Pass',
                'promo_code' => 'ELITEVIP',
                'sort_order' => 3,
            ],
            [
                'bookmaker_id' => $betway?->id,
                'badge' => 'BETWAY BOOST SPECIAL',
                'title' => 'Weekend 100% Multi-Bet Cash Boost',
                'subtitle' => 'Build an accumulator of 5+ games and get your payouts boosted up to 100% on Betway.',
                'highlight' => 'Tshs 500 Min Stake • Live In-Play Payouts',
                'cta_text' => 'Claim Betway Boost',
                'promo_code' => 'BETWAY100',
                'sort_order' => 4,
            ],
        ];

        foreach ($ads as $ad) {
            HomeAd::updateOrCreate(['badge' => $ad['badge']], $ad);
        }
    }
}
