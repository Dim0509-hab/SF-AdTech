<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversion;
use App\Models\Click;



class WebmasterController extends Controller
{

 ///////////////////////
        protected function authorizeUser()
    {
        if (Auth::user()->role !== 'webmaster') {
            abort(403, 'Доступ запрещён');
        }
    }

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorizeUser();
            return $next($request);
        });
    }

    public function index(){ $offers = Offer::where('active',1)
        ->withCount('webmasters')->get(); $subs = Auth::user()
        ->subscriptions()->pluck('offer_id')
        ->toArray(); return view('webmaster.index', compact('offers','subs')); }

        public function offers()
    {
        // Получение активных офферов
        $offers = Offer::where('active', 1)
            ->withCount('webmasters')
            ->get();

        // ID подписок текущего пользователя
        $subs = Auth::user()
            ->subscriptions()
            ->pluck('offer_id')
            ->toArray();

        // Передача данных в шаблон
        return view('webmaster.index', compact('offers', 'subs'));
    }

   public function subscribe(Request $request, $offerId)
{
    $validated = $request->validate([
        'cost_per_click' => ['required', 'numeric', 'min:0.01'],
        'agreed_price'     => ['nullable', 'numeric', 'min:0'], // если есть в форме
    ]);

    $user = Auth::user();
    $offer = Offer::findOrFail($offerId);

    // Подписываемся или обновляем подписку
    $user->subscriptions()->syncWithoutDetaching([
        $offerId => [
            'cost_per_click' => $validated['cost_per_click'],
            'agreed_price'   => $offer->price,
        ]
    ]);

    return redirect()->back()->with('success', 'Подписка оформлена!');
}
    public function unsubscribe($id)
    {
         Auth::user()->subscriptions()->detach($id);
         return back()->with('success','Отписано');
    }
    public function getLink($id)
    {
         $token = 'offer_'.$id.'_wm_'.Auth::id();
         $link = url('/r/'.$token); return view('webmaster.link', compact('link'));
    }
    public function subscribed()
    {
        // Только подписанные офферы + подсчёт подписчиков
        $offers = Auth::user()->offers()
            ->withCount('webmasters') // Количество подписчиков на каждый оффер
            ->get();

        return view('webmaster.subscribed', compact('offers'));
    }

public function stats()
{
    $userId = Auth::id();

    $today = now()->startOfDay();
    $month = now()->startOfMonth();
    $year = now()->startOfYear();

    // Получаем агрегированные данные одним запросом
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

    // Теперь используем $result, а не $stats
    $stats = [
        'today' => [
            'clicks'   => (int)    ($result?->today_clicks   ?? 0),
            'revenue'  => round((float) ($result?->today_revenue  ?? 0), 2),
        ],
        'month' => [
            'clicks'   => (int)    ($result?->month_clicks   ?? 0),
            'revenue'  => round((float) ($result?->month_revenue  ?? 0), 2),
        ],
        'year' => [
            'clicks'   => (int)    ($result?->year_clicks    ?? 0),
            'revenue'  => round((float) ($result?->year_revenue   ?? 0), 2),
        ],
    ];

    // Подписки с детализацией по офферам
    $subscriptions = Auth::user()->subscriptions()
        ->withCount([
            'clicks as today_clicks' => fn($q) => $q->where('created_at', '>=', $today),
            'clicks as month_clicks' => fn($q) => $q->where('created_at', '>=', $month),
            'clicks as year_clicks'  => fn($q) => $q->where('created_at', '>=', $year),
        ])
        ->withSum([
            'clicks as today_revenue' => fn($q) => $q->where('created_at', '>=', $today),
            'clicks as month_revenue' => fn($q) => $q->where('created_at', '>=', $month),
            'clicks as year_revenue'  => fn($q) => $q->where('created_at', '>=', $year),
        ], 'cost')
        ->get();

    return view('webmaster.stats', compact('stats', 'subscriptions'));
}



  public function dashboardStats()
    {
        $userId = Auth::id();

        // Все офферы вебмастера
        $offers = Offer::whereHas('webmasters', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        // Общая статистика
        $totalStats = [
            'total_offers' => $offers->count(),
            'total_clicks' => Click::whereHas('offer.webmasters', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count(),
            'total_conversions' => Conversion::whereHas('offer.webmasters', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count(),
            'total_revenue' => Conversion::whereHas('offer.webmasters', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->sum('revenue'),
        ];

        return view('webmaster.stats', [
            'offers' => $offers,
            'totalStats' => $totalStats,
            'isDashboardStats' => true,  // Флаг для шаблона
        ]);
    }
}

