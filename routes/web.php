<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdvertiserController;
use App\Http\Controllers\WebmasterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AdminOffersController;
use App\Http\Controllers\AdminStatsController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'advertiser') {
            return redirect()->route('advertiser.index');
        } else {
            return redirect()->route('webmaster.index');
        }
    })->name('dashboard');






    // === Рекламодатель ===
    Route::prefix('advertiser')->group(function () {
        Route::get('/offers', [AdvertiserController::class, 'index'])->name('advertiser.index');
        Route::get('/offers/create', [AdvertiserController::class, 'create'])->name('advertiser.create');
        Route::delete('/offers/{id}', [AdvertiserController::class, 'destroy'])->name('advertiser.offers.destroy');
        Route::get('/advertiser/offers/{id}/stats/{period?}', [AdvertiserController::class, 'offerStats'
                 ])->name('advertiser.stats');
        Route::post('/offers', [AdvertiserController::class, 'store'])
            ->name('advertiser.store');
        Route::post('/offers/{id}/activate', [AdvertiserController::class, 'activateOffer'])
                ->name('advertiser.offers.activate');
        Route::post('/offers/{id}/deactivate', [AdvertiserController::class, 'deactivateOffer'])
            ->name('advertiser.offers.deactivate');
    });



    // === Вебмастер ===
    Route::prefix('webmaster')->group(function () {
    Route::get('/offers', [WebmasterController::class, 'index'])
        ->name('webmaster.index');

    Route::post('/offers/{id}/subscribe', [WebmasterController::class, 'subscribe'])
        ->name('webmaster.offers.subscribe');

    Route::post('/offers/{id}/unsubscribe', [WebmasterController::class, 'unsubscribe'])
        ->name('webmaster.offers.unsubscribe');

    Route::get('/offers/{id}/link', [WebmasterController::class, 'getLink'])
        ->name('webmaster.offers.link');

    // Изменено: уникальный суффикс для статистики оффера
    Route::get('/offers/{id}/stats', [WebmasterController::class, 'offerStats'])
        ->name('webmaster.offers.stats');


    // Изменено: уникальный суффикс для общей статистики
    Route::get('/stats', [WebmasterController::class, 'dashboardStats'])
        ->name('webmaster.dashboard.stats');
});





    // === Администратор ===
     Route::prefix('admin')->middleware('auth')->group(function () {
    // Главная страница
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    // Пользователи
    Route::get('/users', [AdminUsersController::class, 'index'])
        ->name('admin.users');
    Route::post('/users/{id}/toggle', [AdminUsersController::class, 'toggleStatus'])
        ->name('admin.users.toggle');
    Route::post('/users/{id}/role', [AdminUsersController::class, 'assignRole'])
        ->name('admin.users.role');

    // Офферы
    Route::get('/offers', [AdminOffersController::class, 'index'])
        ->name('admin.offers');

    // Статистика
    Route::get('/stats', [AdminStatsController::class, 'index'])
        ->name('admin.stats');
        Route::get('/revenue', [AdminStatsController::class, 'index'])
        ->name('admin.revenue.index');
        // Маршрут для экспорта в CSV
    Route::get('/revenue/export', [AdminStatsController::class, 'export'])
        ->name('admin.revenue.export');
});
});
