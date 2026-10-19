<?php

namespace Tests\Feature;

use App\Models\Betslip;
use App\Models\CommunityPoll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_home_feed_returns_ads_featured_match_poll_and_tipsters(): void
    {
        $response = $this->getJson('/api/v1/home');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('currency', 'Tshs')
            ->assertJsonStructure([
                'data' => [
                    'adsBoard' => ['rotationIntervalMs', 'banners'],
                    'featuredMatch' => ['homeTeam', 'awayTeam', 'draw', 'bookmaker'],
                    'communityPoll' => ['homeVotes', 'drawVotes', 'awayVotes', 'totalVotes'],
                    'topTipsters',
                ],
            ]);

        $this->assertNotEmpty($response->json('data.adsBoard.banners'));
        $this->assertNotEmpty($response->json('data.topTipsters'));
    }

    public function test_betslips_hide_vip_booking_codes_for_guests(): void
    {
        $response = $this->getJson('/api/v1/betslips');

        $response->assertOk();

        $vipSlip = collect($response->json('data'))->firstWhere('isVip', true);

        $this->assertNotNull($vipSlip);
        $this->assertSame('******', $vipSlip['bookingCode']);
        $this->assertFalse($vipSlip['isUnlocked']);
    }

    public function test_vip_subscriber_sees_unlocked_booking_codes(): void
    {
        $user = User::query()->where('email', 'juma@example.com')->firstOrFail();

        $response = $this->getJson('/api/v1/betslips', [
            'X-User-Id' => (string) $user->id,
        ]);

        $response->assertOk();

        $vipSlip = collect($response->json('data'))->firstWhere('isVip', true);

        $this->assertNotNull($vipSlip);
        $this->assertTrue($vipSlip['isUnlocked']);
        $this->assertNotSame('******', $vipSlip['bookingCode']);
    }

    public function test_profile_returns_demo_user_purchases_and_transactions(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.email', 'juma@example.com')
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'walletBalance'],
                    'stats' => ['purchasedSlips', 'activeSlips', 'wonSlips', 'totalSpent', 'totalDeposited'],
                    'vip' => ['isActive', 'planName', 'expiresAt'],
                    'purchasedSlips',
                    'transactions',
                ],
            ]);
    }

    public function test_user_can_unlock_a_vip_betslip(): void
    {
        $user = User::query()->where('email', 'juma@example.com')->firstOrFail();
        $slip = Betslip::query()->where('is_free', false)->firstOrFail();

        $response = $this->postJson("/api/v1/betslips/{$slip->id}/unlock", [
            'user_id' => $user->id,
            'payment_provider' => 'mpesa',
            'phone' => '+255712345678',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.bookingCode', $slip->booking_code);

        $this->assertDatabaseHas('purchased_slips', [
            'user_id' => $user->id,
            'betslip_id' => $slip->id,
        ]);
    }

    public function test_user_can_vote_on_the_live_community_poll(): void
    {
        $poll = CommunityPoll::query()->where('is_live', true)->firstOrFail();
        $previousTotal = $poll->total_votes_count;

        $response = $this->postJson("/api/v1/polls/{$poll->id}/vote", [
            'choice' => '1',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.totalVotes', $previousTotal + 1);
    }

    public function test_vip_plans_and_subscription_are_available(): void
    {
        $plansResponse = $this->getJson('/api/v1/vip/plans');

        $plansResponse->assertOk();
        $plans = collect($plansResponse->json('data'));

        $weekly = $plans->firstWhere('id', 'weekly');

        $this->assertNotNull($weekly);

        $user = User::query()->where('email', 'juma@example.com')->firstOrFail();

        $subscribeResponse = $this->postJson('/api/v1/vip/subscribe', [
            'user_id' => $user->id,
            'plan_slug' => 'weekly',
            'payment_provider' => 'mpesa',
        ]);

        $subscribeResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.planSlug', 'weekly');

        $this->assertDatabaseHas('user_vip_subscriptions', [
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }
}
