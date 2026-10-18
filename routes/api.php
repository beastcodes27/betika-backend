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

    // Ads board tracking
    Route::post('/ads/{homeAd}/click', [MobileApiController::class, 'trackAdClick'])->name('api.ads.click');

    // Community voting poll
    Route::post('/polls/{communityPoll}/vote', [MobileApiController::class, 'votePoll'])->name('api.polls.vote');

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
