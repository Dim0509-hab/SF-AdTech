<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Click;
use App\Models\Conversion;
use App\Models\View;
use App\Models\AdSpend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
    $this->middleware(function ($request, $next) { $this->authorizeUser();
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
        return view('advertiser.index', compact('offers'));
    }
    /**
     * Форма создания оффера
     */
    public function create()
    {
        return view('advertiser.create');
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

    // Генерируем уникальный link_hash
    $linkHash = '';
    do {
        $linkHash = Str::random(6); // например: "a1b2c3"
    } while (Offer::where('link_hash', $linkHash)->exists());

    Offer::create([
        'advertiser_id' => Auth::id(),
        'name' => $request->name,
        'price' => $request->price,
        'target_url' => $request->target_url,
        'themes' => $request->input('themes', []),
        'active' => true,
        'link_hash' => $linkHash, // ✅ Теперь переменная определена
    ]);

    return redirect()
        ->route('advertiser.index')
        ->with('success', 'Оффер создан!');
}

    public function destroy($id)
    {
        $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($id);
        $offer->delete();

        return redirect()
            ->route('advertiser.index')
            ->with('success', 'Оффер удалён!');
    }

    public function stats($offerId)
    {
        $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($offerId);
        $stats = $offer->getStats();

        return view('advertiser.stats', compact('offer', 'stats'));
    }
    public function activateOffer($id)
{
    $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($id);

    $offer->update(['active' => true]);

    return redirect()
        ->route('advertiser.index')
        ->with('success', 'Оффер успешно активирован');
}


    public function deactivateOffer($id)
    {
        $offer = Offer::where('advertiser_id', Auth::id())->findOrFail($id);
        $offer->update(['active' => false]);

        return redirect()
            ->route('advertiser.index')
            ->with('success', 'Оффер деактивирован');
    }
   public function offerStats($id, $period = 'day')
{
    // 1. Валидация периода
    $validPeriods = ['day', 'month', 'year'];
    $period = in_array($period, $validPeriods) ? $period : 'day';

    // 2. Поиск оффера (с проверкой прав)
    $offer = Offer::where('advertiser_id', Auth::id())
        ->findOrFail($id);

    // 3. Определение даты начала периода
    $now = now();
    $startDate = match ($period) {
        'day' => $now->startOfDay(),
        'month' => $now->startOfMonth(),
        'year' => $now->startOfYear(),
    };

    // 4. Сбор статистики с защитой от пустых значений
    $stats = [
        // Просмотры
        'views' => View::query()
            ->where('offer_id', $offer->id)
            ->where('created_at', '>=', $startDate)
            ->count(),

        // Клики (сумма поля `count`)
        'clicks' => Click::query()
            ->where('offer_id', $offer->id)
            ->where('created_at', '>=', $startDate)
            ->sum('count') ?: 0,

        // Конверсии
        'conversions' => Conversion::query()
            ->where('offer_id', $offer->id)
            ->where('created_at', '>=', $startDate)
            ->count(),

        // Доход (сумма `revenue`)
        'revenue' => Conversion::query()
            ->where('offer_id', $offer->id)
            ->where('created_at', '>=', $startDate)
            ->sum('revenue') ?: 0,

        // Затраты (сумма `amount`)
        'cost' => AdSpend::query()
            ->where('offer_id', $offer->id)
            ->where('date', '>=', $startDate)
            ->sum('amount') ?: 0,
    ];

    return view('advertiser.stats', compact('offer', 'stats', 'period'));
}


}


