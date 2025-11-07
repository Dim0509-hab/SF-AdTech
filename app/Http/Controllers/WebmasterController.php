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
            'agreed_price'   => $validated['agreed_price'] ?? 0.00, // добавляем!
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
        $webmasterId = Auth::id();


        // Получаем подписки веб‑мастера с офферами и кликами
        $subscriptions = Subscription::where('webmaster_id', $webmasterId)
            ->with(['offer', 'clicks'])
            ->get();

        // Считаем статистику
        $today = now()->startOfDay();
        $month = now()->startOfMonth();
        $year = now()->startOfYear();

        $stats = [
            'today' => [
                'clicks' => 0,
                'revenue' => 0,
            ],
            'month' => [
                'clicks' => 0,
                'revenue' => 0,
            ],
            'year' => [
                'clicks' => 0,
                'revenue' => 0,
            ],
        ];

        foreach ($subscriptions as $sub) {
            $pricePerClick = $sub->cost_per_click;

            // Сегодня
            $todayClicks = $sub->clicks->where('created_at', '>=', $today)->count();
            $stats['today']['clicks'] += $todayClicks;
            $stats['today']['revenue'] += $todayClicks * $pricePerClick;

            // Месяц
            $monthClicks = $sub->clicks->where('created_at', '>=', $month)->count();
            $stats['month']['clicks'] += $monthClicks;
            $stats['month']['revenue'] += $monthClicks * $pricePerClick;

            // Год
            $yearClicks = $sub->clicks->where('created_at', '>=', $year)->count();
            $stats['year']['clicks'] += $yearClicks;
            $stats['year']['revenue'] += $yearClicks * $pricePerClick;
        }

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

