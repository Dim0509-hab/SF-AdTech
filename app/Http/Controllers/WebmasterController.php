<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;



class WebmasterController extends Controller {
    public function index(){ $offers = Offer::where('active',1)->withCount('webmasters')->get(); $subs = Auth::user()->subscriptions()->pluck('offer_id')->toArray(); return view('webmaster.offers.index', compact('offers','subs')); }
    public function subscribe($id){ Auth::user()->subscriptions()->attach($id, ['agreed_price'=>Offer::findOrFail($id)->price]); return back()->with('success','Подписано'); }
    public function unsubscribe($id){ Auth::user()->subscriptions()->detach($id); return back()->with('success','Отписано'); }
    public function getLink($id){ $token = 'offer_'.$id.'_wm_'.Auth::id(); $link = url('/r/'.$token); return view('webmaster.offers.link', compact('link')); }
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



    public function stats($id){ $data = \App\Models\Click::selectRaw('DATE(clicked_at) as day, COUNT(*) as clicks, SUM(cost) as earned')->where('webmaster_id', Auth::id())->where('offer_id',$id)->groupBy('day')->orderBy('day','desc')->get(); return view('webmaster.offers.stats', compact('data')); }
}

