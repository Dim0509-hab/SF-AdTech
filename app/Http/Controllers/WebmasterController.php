<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use App\Models\Click;
use Illuminate\Support\Facades\DB;


class WebmasterController extends Controller
{
    // === Авторизация ===
    protected function authorizeUser()
    {
        if (Auth::user()->role !== 'webmaster') {
            abort(403, 'Доступ запрещён: только для веб-мастеров');
        }
    }

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorizeUser();
            return $next($request);
        });
    }

    // === Главная страница: список офферов ===
    public function index()
    {
        $offers = Offer::where('active', 1)
            ->withCount('webmasters')
            ->get();

        $subs = Auth::user()->offers()->pluck('offer_id')->toArray();

        return view('webmaster.index', compact('offers', 'subs'));
    }

    // === Подписка на оффер ===
    public function subscribe(Request $request, $offerId)
    {
         /** @var \App\Models\User $user */

        $offer = Offer::findOrFail($offerId);

        // Валидация
        $request->validate([
            'cost_per_click' => [
                'required',
                'numeric',
                'min:' . ($offer->price * 0.5),  // минимум 50% от базы
                'max:' . ($offer->price * 3.0),  // максимум 300% от базы
            ],
        ], [
            'cost_per_click.min' => 'Ставка не может быть меньше 50% от базовой цены.',
            'cost_per_click.max' => 'Ставка не может превышать 300% от базовой цены.',
        ]);

        // Проверка на дубль
        $exists = DB::table('offer_webmaster')
            ->where('webmaster_id', auth()->id())
            ->where('offer_id', $offerId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Вы уже подписаны на этот оффер.');
        }

        // Создаём подписку
        DB::table('offer_webmaster')->insert([
            'webmaster_id' => auth()->id(),
            'offer_id' => $offerId,
            'cost_per_click' => $request->cost_per_click,
            'agreed_price' => $request->cost_per_click,
            'status' => 'active', // или 'pending' — по вашей логике
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Вы подписались со ставкой ' . number_format($request->cost_per_click, 2, ',', ' ') . ' ₽!');
    }


    // === Отписка ===
    public function unsubscribe($offerId)
    {
        Auth::user()->offers()->detach($offerId);
        return redirect()->back()->with('success', 'Вы отписались от оффера');
    }

    // === Генерация партнёрской ссылки ===
    public function getLink($offerId)
    {
        $offer = Offer::findOrFail($offerId);
        $token = "offer_{$offerId}_wm_" . Auth::id();
        $link = url('/r/' . $token);

        return view('webmaster.link', compact('link', 'offer'));
    }

    // === Мои подписки ===
   public function subscribed()
{
    $offers = Auth::user()->offers()
        ->withCount([
            'clicks as today_clicks' => fn($q) => $q->where('created_at', '>=', now()->startOfDay()),
            'clicks as month_clicks' => fn($q) => $q->where('created_at', '>=', now()->startOfMonth()),
            'clicks as year_clicks'  => fn($q) => $q->where('created_at', '>=', now()->startOfYear()),
        ])
        ->withSum([
            'clicks as today_revenue' => fn($q) => $q->where('created_at', '>=', now()->startOfDay()),
            'clicks as month_revenue' => fn($q) => $q->where('created_at', '>=', now()->startOfMonth()),
            'clicks as year_revenue'  => fn($q) => $q->where('created_at', '>=', now()->startOfYear()),
        ], 'cost')
        ->get()
        ->map(function ($offer) {
            $offer->webmasters_count = $offer->webmasters()->count();
            return $offer;
        });

    return view('webmaster.subscribed', compact('offers'));
}



    // === Статистика ===
public function stats()
{
    $userId = Auth::id();
    $today = now()->startOfDay();
    $month = now()->startOfMonth();
    $year = now()->startOfYear();

    // Общая статистика
    $result = Click::where('webmaster_id', $userId)
        ->selectRaw("
            COUNT(*) as total_clicks,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as today_clicks,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as month_clicks,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as year_clicks,
            SUM(cost) as total_revenue,
            SUM(CASE WHEN created_at >= ? THEN cost ELSE 0 END) as today_revenue,
            SUM(CASE WHEN created_at >= ? THEN cost ELSE 0 END) as month_revenue,
            SUM(CASE WHEN created_at >= ? THEN cost ELSE 0 END) as year_revenue
        ", [$today, $month, $year, $today, $month, $year])
        ->first();

    $stats = [
        'today' => [
            'clicks'  => (int) ($result?->today_clicks ?? 0),
            'revenue' => round((float) ($result?->today_revenue ?? 0), 2),
        ],
        'month' => [
            'clicks'  => (int) ($result?->month_clicks ?? 0),
            'revenue' => round((float) ($result?->month_revenue ?? 0), 2),
        ],
        'year' => [
            'clicks'  => (int) ($result?->year_clicks ?? 0),
            'revenue' => round((float) ($result?->year_revenue ?? 0), 2),
        ],
    ];

    // Статистика по офферам
    $subscriptions = Auth::user()->offers()
        ->withCount([
            'clicks as today_clicks' => fn($q) => $q->where('created_at', '>=', $today),
            'clicks as month_clicks' => fn($q) => $q->where('created_at', '>=', $month),
            'clicks as year_clicks'  => fn($q) => $q->where('created_at', '>=', $year),
            'conversions as today_conversions',
            'conversions as month_conversions',
            'conversions as year_conversions'
        ])
        ->withSum([
            'clicks as today_revenue' => fn($q) => $q->where('created_at', '>=', $today),
            'clicks as month_revenue' => fn($q) => $q->where('created_at', '>=', $month),
            'clicks as year_revenue'  => fn($q) => $q->where('created_at', '>=', $year),
        ], 'cost')
        ->get();

    return view('webmaster.stats', compact('stats', 'subscriptions'));
}


}
