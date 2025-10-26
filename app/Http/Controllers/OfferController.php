<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Click;
use App\Models\Conversion;



class OfferController extends Controller
{
    /**
     * Показать список офферов рекламодателя.
     */
    public function index()
    {
        $offers = Offer::where('user_id', Auth::id())
            ->withCount('subscriptions')
            ->get();

        return view('advertiser.offers', compact('offers'));
    }

    /**
     * Деактивировать оффер.
     */
    public function deactivate(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        if ($offer->user_id !== Auth::id()) {
            abort(403, 'Доступ запрещён');
        }

        $offer->update(['active' => false]);

        return redirect()->route('advertiser.offers')
            ->with('success', 'Оффер деактивирован');
    }

    /**
     * Показать статистику по офферу.
     */
    public function stats($id)
{
    $offer = Offer::findOrFail($id);

    // Проверка прав: оффер должен принадлежать текущему пользователю
    if ($offer->user_id !== Auth::id()) {
        abort(403, 'У вас нет доступа к статистике этого оффера');
    }

    // Расчёт статистики
    $stats = [
        'views' => Click::where('offer_id', $id)
            ->whereDate('created_at', '>=', now()->subDay())
            ->count(),

        'clicks' => Click::where('offer_id', $id)
            ->whereDate('created_at', '>=', now()->subDay())
            ->whereNotNull('click_id') // если есть отдельное поле для клика
            ->count(),

        'conversions' => Conversion::where('offer_id', $id) // предполагаем модель Conversion
            ->whereDate('created_at', '>=', now()->subDay())
            ->count(),

        'revenue' => Click::where('offer_id', $id)
            ->whereDate('created_at', '>=', now()->subDay())
            ->sum('cost'), // предполагаем поле `cost` в таблице clicks
    ];

    return view('advertiser.stats', compact('offer', 'stats'));
}

}
