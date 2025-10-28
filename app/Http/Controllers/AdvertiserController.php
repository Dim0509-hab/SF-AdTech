<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdvertiserController extends Controller
{
    /**
     * Метод для проверки прав доступа
     */
    protected function authorizeUser()
{
    if (Auth::user()->role !== 'advertiser') {
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


    /**
     * Главная страница рекламодателя
     */

   public function index()
{
    $offers = Offer::where('advertiser_id', Auth::id())
        ->withCount('webmasters')
        ->paginate(10);
    return view('advertiser.offers.index', compact('offers'));
}
/**
     * Форма создания оффера
     */
    public function create()
    {
        return view('advertiser.offers.create');
    }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0.01',
        'target_url' => 'required|url|active_url',
        'themes' => 'array|nullable'
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    Offer::create([
        'advertiser_id' => Auth::id(),
        'name' => $request->name,
        'price' => $request->price,
        'target_url' => $request->target_url,
        'themes' => $request->input('themes', []),
        'active' => true,
    ]);

    return redirect()
        ->route('advertiser.offers.index')
        ->with('success', 'Оффер создан!');
}

public function destroy($id)
{
    $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($id);
    $offer->delete();

    return redirect()
        ->route('advertiser.offers.index')
        ->with('success', 'Оффер удалён!');
}

public function stats($offerId)
{
    $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($offerId);
    $stats = $offer->getStats(); // ваша логика

    return view('advertiser.offers.stats', compact('offer', 'stats'));
}

public function deactivateOffer($id)
{
    $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($id);
    $offer->update(['active' => false]);

    return redirect()
        ->route('advertiser.offers.index')
        ->with('success', 'Оффер деактивирован');
}
public function activateOffer($id)
{
    $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($id);

    $offer->update(['active' => true]);

    return redirect()
        ->route('advertiser.offers.index')
        ->with('success', 'Оффер успешно активирован');
}




}


