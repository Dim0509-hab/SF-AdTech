<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;



class WebmasterController extends Controller {

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
    public function subscribe($id)
    {
         Auth::user()
        ->subscriptions()->attach($id, ['agreed_price'=>Offer::findOrFail($id)->price]);
         return back()->with('success','Подписано');
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


}

