<?php

namespace Database\Seeders;

use App\Models\Betslip;
use App\Models\PurchasedSlip;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserVipSubscription;
use App\Models\VipPlan;
use Illuminate\Database\Seeder;

class VipPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'single',
                'name' => 'Single Slip Access',
                'price' => 2000.00,
                'currency' => 'Tshs',
                'duration_days' => 1,
                'description' => 'Instant unlock for a single VIP ticket & booking code',
            ],
            [
                'slug' => 'weekly',
                'name' => 'Weekly VIP Pass',
                'price' => 7500.00,
                'currency' => 'Tshs',
                'duration_days' => 7,
                'description' => 'Unlimited VIP tickets for 7 full days',
            ],
            [
                'slug' => 'monthly',
                'name' => 'Monthly VIP Club',
                'price' => 20000.00,
                'currency' => 'Tshs',
                'duration_days' => 30,
                'description' => 'Complete VIP access to all tipster bankers & slips for 30 days',
            ],
        ];

        foreach ($plans as $plan) {
            VipPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        // Sample user purchase and subscription
        $user = User::where('email', 'juma@example.com')->first();
        $singlePlan = VipPlan::where('slug', 'single')->first();
        $slip = Betslip::where('category', 'VIP')->first();

        if ($user && $singlePlan && $slip) {
            UserVipSubscription::updateOrCreate(
                ['user_id' => $user->id, 'vip_plan_id' => $singlePlan->id],
                [
                    'started_at' => now(),
                    'expires_at' => now()->addDay(),
                    'amount_paid' => 2000.00,
                    'status' => 'active',
                ]
            );

            PurchasedSlip::updateOrCreate(
                ['user_id' => $user->id, 'betslip_id' => $slip->id],
                [
                    'amount_paid' => 'Tshs 2,000',
                    'booking_code_unlocked' => $slip->booking_code,
                    'status' => 'active',
                    'purchased_at' => now(),
                ]
            );

            Transaction::updateOrCreate(
                ['reference' => 'MPESA-QJ94827101'],
                [
                    'user_id' => $user->id,
                    'type' => 'slip_purchase',
                    'description' => 'Purchased VIP Slip Access (' . $slip->title . ')',
                    'method' => 'M-Pesa',
                    'phone_number' => '+255712345678',
                    'amount' => 2000.00,
                    'currency' => 'Tshs',
                    'status' => 'completed',
                ]
            );
        }
    }
}
