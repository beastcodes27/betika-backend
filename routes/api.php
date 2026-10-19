<?php

use App\Http\Controllers\Api\MobileApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile REST API Routes
|--------------------------------------------------------------------------
|
| Endpoints consumed by the React Native mobile application for BetWinner.
| Provides home feed, betslips, VIP access paywall, tipster profiles,
| auto-scrolling ads board, and community voting polls.
| Currency strictly in Tshs.
|
*/

Route::prefix('v1')->group(function () {
    // Public home feed
    Route::get('/home', [MobileApiController::class, 'home'])->name('api.home');

    // Betslips & VIP reveal logic
    Route::get('/betslips', [MobileApiController::class, 'betslips'])->name('api.betslips');
    Route::post('/betslips/{betslip}/unlock', [MobileApiController::class, 'unlockSlip'])->name('api.betslips.unlock');

    // Verified tipsters feed
    Route::get('/tipsters', [MobileApiController::class, 'tipsters'])->name('api.tipsters');

    // Mobile user profile, purchased slips & wallet ledger
    Route::get('/profile', [MobileApiController::class, 'profile'])->name('api.profile');
    Route::post('/profile/avatar', [MobileApiController::class, 'uploadProfileAvatar'])->name('api.profile.avatar');
    Route::post('/profile/avatar/remove', [MobileApiController::class, 'removeProfileAvatar'])->name('api.profile.avatar.remove');

    // VIP plans & subscriptions
    Route::get('/vip/plans', [MobileApiController::class, 'vipPlans'])->name('api.vip.plans');
    Route::post('/vip/subscribe', [MobileApiController::class, 'subscribeVip'])->name('api.vip.subscribe');

    // Ads board tracking
    Route::post('/ads/{homeAd}/click', [MobileApiController::class, 'trackAdClick'])->name('api.ads.click');

    // Community voting poll
    Route::post('/polls/{communityPoll}/vote', [MobileApiController::class, 'votePoll'])->name('api.polls.vote');

    // Tipster profile picture upload from mobile client
    Route::post('/tipster/avatar', [MobileApiController::class, 'uploadAvatar'])->name('api.tipster.avatar');

    // Health check
    Route::get('/status', function () {
        return response()->json([
            'status' => 'online',
            'platform' => config('app.name'),
            'currency' => config('app.currency', 'Tshs'),
            'timestamp' => now()->toIso8601String(),
        ]);
    });
});
