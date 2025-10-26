<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdvertiserController;
use App\Http\Controllers\WebmasterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RedirectController;


// Главная
Route::get('/', function () {
    return view('welcome');
});

// Авторизация
require __DIR__ . '/auth.php';

// Редирект по токену
Route::get('/r/{token}', [RedirectController::class, 'redirect'])->name('redirect');


// === Авторизованные пользователи ===
Route::middleware(['auth'])->group(function () {

    // Дашборд с перенаправлением по роли
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();


        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'advertiser') {
            return redirect()->route('advertiser.offers.index');
        } else {
            return redirect()->route('webmaster.offers.index');
        }
    })->name('dashboard');

    // === Рекламодатель ===
    Route::prefix('advertiser')->group(function () {
        Route::get('/offers', [AdvertiserController::class, 'index'])->name('advertiser.offers.index');
        Route::get('/offers/create', [AdvertiserController::class, 'create'])->name('advertiser.offers.create');
        Route::post('/offers', [AdvertiserController::class, 'store'])->name('advertiser.offers.store');
        Route::post('/offers/{id}/deactivate', [AdvertiserController::class, 'deactivate'])->name('advertiser.offers.deactivate');
        Route::get('/offers/{id}/stats', [AdvertiserController::class, 'stats'])->name('advertiser.offers.stats');
    });

    // === Вебмастер ===
    Route::prefix('webmaster')->group(function () {
        Route::get('/offers', [WebmasterController::class, 'index'])->name('webmaster.offers.index');
        Route::post('/offers/{id}/subscribe', [WebmasterController::class, 'subscribe'])->name('webmaster.offers.subscribe');
        Route::post('/offers/{id}/unsubscribe', [WebmasterController::class, 'unsubscribe'])->name('webmaster.offers.unsubscribe');
        Route::get('/offers/{id}/link', [WebmasterController::class, 'getLink'])->name('webmaster.offers.link');
        Route::get('/offers/{id}/stats', [WebmasterController::class, 'stats'])->name('webmaster.offers.stats');
    });

    // === Администратор ===
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleActive'])->name('admin.users.toggle');
        Route::get('/offers', [AdminController::class, 'offers'])->name('admin.offers');
        Route::get('/stats', [AdminController::class, 'systemStats'])->name('admin.stats');
    });
});
