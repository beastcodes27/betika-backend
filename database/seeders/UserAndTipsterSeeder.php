<?php

namespace Database\Seeders;

use App\Models\TipsterProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAndTipsterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@betwinner.co.tz'],
            [
                'name' => 'BetWinner Super Admin',
                'phone' => '+255754000111',
                'role' => 'super_admin',
                'wallet_balance' => 15000000.00,
                'is_active' => true,
                'status' => 'active',
                'password' => Hash::make('Admin@2026!'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Tipsters Managed by Super Admin
        $tipsters = [
            [
                'name' => 'Captain Odds',
                'email' => 'captain@betwinner.co.tz',
                'phone' => '+255754112233',
                'handle' => '@captain_odds',
                'specialty' => 'European Leagues & Banker Combos',
                'avatar_bg' => '#3B82F6',
                'win_rate' => 86.00,
                'badge' => 'TOP TIPSTER 🔥',
                'streak' => '7W Streak',
                'followers_count' => 24500,
                'is_verified' => true,
                'can_publish' => true,
                'status' => 'approved',
                'bio' => 'Professional football betting analyst specializing in Premier League, Champions League, and high-win rate doubles.',
            ],
            [
                'name' => 'Sniper Bets',
                'email' => 'sniper@betwinner.co.tz',
                'phone' => '+255754223344',
                'handle' => '@sniper_bets',
                'specialty' => 'High-Confidence 2-Odds Doubles',
                'avatar_bg' => '#EC4899',
                'win_rate' => 91.00,
                'badge' => 'STREAK 9W 🔥',
                'streak' => '9W Streak',
                'followers_count' => 31800,
                'is_verified' => true,
                'can_publish' => true,
                'status' => 'approved',
                'bio' => 'Sniper accuracy predictions with strict bankroll management. 9 consecutive winning tickets.',
            ],
            [
                'name' => 'Goal Master AI',
                'email' => 'goalmaster@betwinner.co.tz',
                'phone' => '+255754334455',
                'handle' => '@goal_master',
                'specialty' => 'Over 2.5 & Both Teams to Score',
                'avatar_bg' => '#8B5CF6',
                'win_rate' => 82.00,
                'badge' => 'AI PREDICTOR 🤖',
                'streak' => '5W Streak',
                'followers_count' => 18200,
                'is_verified' => true,
                'can_publish' => true,
                'status' => 'approved',
                'bio' => 'Algorithmic statistical model analyzing team xG, defensive vulnerabilities, and goal trends across 25 leagues.',
            ],
            [
                'name' => 'VIP Elite Club',
                'email' => 'vipelite@betwinner.co.tz',
                'phone' => '+255754445566',
                'handle' => '@vip_elite',
                'specialty' => 'Weekend High Odds Accumulators',
                'avatar_bg' => '#F59E0B',
                'win_rate' => 88.00,
                'badge' => 'EXCLUSIVE VIP 👑',
                'streak' => '12W Streak',
                'followers_count' => 45100,
                'is_verified' => true,
                'can_publish' => true,
                'status' => 'approved',
                'bio' => 'Private insider club tickets with high odd multi-bets and deep league coverage for serious investors.',
            ],
            [
                'name' => 'Tactical Draw Pro',
                'email' => 'draws@betwinner.co.tz',
                'phone' => '+255754556677',
                'handle' => '@draw_tactics',
                'specialty' => 'System Bets & High Value Single Picks',
                'avatar_bg' => '#10B981',
                'win_rate' => 75.00,
                'badge' => 'DRAW SPECIALIST 🎯',
                'streak' => '4W Streak',
                'followers_count' => 14300,
                'is_verified' => true,
                'can_publish' => true,
                'status' => 'approved',
                'bio' => 'High-yield value draw strategy using defensive matrixes and point-sharing incentives.',
            ],
        ];

        foreach ($tipsters as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'role' => 'tipster',
                    'wallet_balance' => 250000.00,
                    'is_active' => true,
                    'status' => 'active',
                    'password' => Hash::make('Tipster@2026!'),
                    'email_verified_at' => now(),
                ]
            );

            TipsterProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'handle' => $data['handle'],
                    'display_name' => $data['name'],
                    'bio' => $data['bio'],
                    'specialty' => $data['specialty'],
                    'avatar_bg' => $data['avatar_bg'],
                    'win_rate' => $data['win_rate'],
                    'badge' => $data['badge'],
                    'streak' => $data['streak'],
                    'followers_count' => $data['followers_count'],
                    'is_verified' => $data['is_verified'],
                    'can_publish' => $data['can_publish'],
                    'status' => $data['status'],
                    'approved_by' => $superAdmin->id,
                    'approved_at' => now(),
                ]
            );
        }

        // 3. Regular Demo Mobile Bettor User
        User::updateOrCreate(
            ['email' => 'juma@example.com'],
            [
                'name' => 'Juma Rashid',
                'phone' => '+255712345678',
                'role' => 'user',
                'wallet_balance' => 45000.00,
                'is_active' => true,
                'status' => 'active',
                'password' => Hash::make('User@2026!'),
                'email_verified_at' => now(),
            ]
        );
    }
}
