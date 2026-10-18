<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BetslipAdminController;
use App\Http\Controllers\Admin\BookmakerAdminController;
use App\Http\Controllers\Admin\CommunityPollAdminController;
use App\Http\Controllers\Admin\FeaturedMatchAdminController;
use App\Http\Controllers\Admin\HomeAdAdminController;
use App\Http\Controllers\Admin\TipsterAdminController;
use App\Http\Controllers\Admin\TransactionAdminController;
use App\Http\Controllers\Admin\VipAdminController;
use App\Http\Controllers\Tipster\TipsterDashboardController;
use App\Http\Controllers\Tipster\TipsterProfileController;
use App\Http\Controllers\Tipster\TipsterSlipController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BetWinner Super Admin & Tipster Portal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Quick role simulation / dev authentication switch
Route::get('/switch-user/{role}', function ($role) {
    $user = User::whereHas('role', function ($q) use ($role) {
        $q->where('slug', $role);
    })->first();

    if ($user) {
        Auth::login($user);
        if ($role === 'super_admin') {
            return redirect()->route('admin.dashboard')->with('success', "Logged in as Super Admin: {$user->name}");
        } elseif ($role === 'tipster') {
            return redirect()->route('tipster.dashboard')->with('success', "Logged in as Tipster: {$user->name}");
        }
    }

    return redirect('/')->with('error', "No user found with role '{$role}'");
})->name('switch.user');

// -------------------------------------------------------------
// Super Admin Panel Routes (Protected by role:super_admin)
// -------------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Metrics
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Tipster Management (Approvals, Badges, Suspension)
    Route::prefix('tipsters')->name('tipsters.')->group(function () {
        Route::get('/', [TipsterAdminController::class, 'index'])->name('index');
        Route::post('/{tipster}/approve', [TipsterAdminController::class, 'approve'])->name('approve');
        Route::post('/{tipster}/reject', [TipsterAdminController::class, 'reject'])->name('reject');
        Route::post('/{tipster}/toggle-badge', [TipsterAdminController::class, 'toggleBadge'])->name('toggle-badge');
        Route::post('/{tipster}/toggle-suspend', [TipsterAdminController::class, 'toggleSuspend'])->name('toggle-suspend');
        Route::post('/{tipster}/reset-avatar', [TipsterAdminController::class, 'resetAvatar'])->name('reset-avatar');
    });

    // Betslip Management & Status Updates
    Route::prefix('betslips')->name('betslips.')->group(function () {
        Route::get('/', [BetslipAdminController::class, 'index'])->name('index');
        Route::post('/{betslip}/status', [BetslipAdminController::class, 'updateStatus'])->name('status');
        Route::post('/{betslip}/toggle-vip', [BetslipAdminController::class, 'toggleVip'])->name('toggle-vip');
        Route::delete('/{betslip}', [BetslipAdminController::class, 'destroy'])->name('destroy');
    });

    // Featured Big Matches & Venue Showcase
    Route::prefix('featured')->name('featured.')->group(function () {
        Route::get('/', [FeaturedMatchAdminController::class, 'index'])->name('index');
        Route::post('/', [FeaturedMatchAdminController::class, 'store'])->name('store');
        Route::post('/{match}/toggle', [FeaturedMatchAdminController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{match}', [FeaturedMatchAdminController::class, 'destroy'])->name('destroy');
    });

    // Community Match Voting Polls
    Route::prefix('polls')->name('polls.')->group(function () {
        Route::get('/', [CommunityPollAdminController::class, 'index'])->name('index');
        Route::post('/', [CommunityPollAdminController::class, 'store'])->name('store');
        Route::post('/{poll}/toggle', [CommunityPollAdminController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{poll}', [CommunityPollAdminController::class, 'destroy'])->name('destroy');
    });

    // Auto-scrolling Ads Board (3s rotation) & Promo Codes
    Route::prefix('ads')->name('ads.')->group(function () {
        Route::get('/', [HomeAdAdminController::class, 'index'])->name('index');
        Route::post('/', [HomeAdAdminController::class, 'store'])->name('store');
        Route::post('/{ad}/toggle', [HomeAdAdminController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{ad}', [HomeAdAdminController::class, 'destroy'])->name('destroy');
    });

    // VIP Subscriptions & Slip Passes
    Route::prefix('vip')->name('vip.')->group(function () {
        Route::get('/', [VipAdminController::class, 'index'])->name('index');
        Route::post('/grant', [VipAdminController::class, 'grantVip'])->name('grant');
        Route::post('/{subscription}/revoke', [VipAdminController::class, 'revokeVip'])->name('revoke');
    });

    // Financial Transaction Ledger (Tshs, M-Pesa, Airtel Money)
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionAdminController::class, 'index'])->name('index');
        Route::post('/{transaction}/complete', [TransactionAdminController::class, 'markAsCompleted'])->name('complete');
        Route::post('/{transaction}/fail', [TransactionAdminController::class, 'markAsFailed'])->name('fail');
    });

    // Bookmakers & Betting Companies (Betika, SportyBet, 1xBet, MozzartBet, Betway)
    Route::prefix('bookmakers')->name('bookmakers.')->group(function () {
        Route::get('/', [BookmakerAdminController::class, 'index'])->name('index');
        Route::post('/', [BookmakerAdminController::class, 'store'])->name('store');
        Route::post('/{bookmaker}/toggle', [BookmakerAdminController::class, 'toggleActive'])->name('toggle');
        Route::put('/{bookmaker}', [BookmakerAdminController::class, 'update'])->name('update');
    });
});

// -------------------------------------------------------------
// Tipster Publishing Portal Routes
// -------------------------------------------------------------
Route::prefix('tipster')->name('tipster.')->group(function () {
    Route::get('/dashboard', [TipsterDashboardController::class, 'index'])->name('dashboard');
    Route::get('/slips/create', [TipsterSlipController::class, 'create'])->name('slips.create');
    Route::post('/slips', [TipsterSlipController::class, 'store'])->name('slips.store');
    Route::delete('/slips/{betslip}', [TipsterSlipController::class, 'destroy'])->name('slips.destroy');

    // Tipster Profile Management & Avatar Upload
    Route::get('/profile', [TipsterProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [TipsterProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [TipsterProfileController::class, 'uploadAvatar'])->name('profile.upload-avatar');
    Route::post('/profile/avatar/remove', [TipsterProfileController::class, 'removeAvatar'])->name('profile.remove-avatar');
});
