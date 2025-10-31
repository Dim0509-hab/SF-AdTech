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


public function stats($offerId = null)
{
    $webmasterId = Auth::id();

    // Если ID оффера не передан — показываем список всех подписанных офферов
    if (!$offerId) {
        $subscriptions = Subscription::where('webmaster_id', $webmasterId)
            ->with('offer')
            ->get();

        return view('webmaster.stats_list', compact('subscriptions'));
    }

    // Иначе — статистика по конкретному офферу
    $subscription = Subscription::where('webmaster_id', $webmasterId)
        ->where('offer_id', $offerId)
        ->with('offer')
        ->first();

    if (!$subscription) {
        abort(404, 'Вы не подписаны на этот оффер');
    }

    $today = now()->startOfDay();
    $month = now()->startOfMonth();
    $year = now()->startOfYear();

    $clicks = $subscription->clicks();

    $pricePerClick = $subscription->cost_per_click;


    $stats = [
        'today' => [
            'clicks' => $clicks->where('created_at', '>=', $today)->count(),
            'revenue' => $clicks->where('created_at', '>=', $today)->count() * $pricePerClick,
        ],
        'month' => [
            'clicks' => $clicks->where('created_at', '>=', $month)->count(),
            'revenue' => $clicks->where('created_at', '>=', $month)->count() * $pricePerClick,
        ],
        'year' => [
            'clicks' => $clicks->where('created_at', '>=', $year)->count(),
            'revenue' => $clicks->where('created_at', '>=', $year)->count() * $pricePerClick,
        ],
    ];

    return view('webmaster.stats_list', compact('subscription', 'stats'));
}

}

