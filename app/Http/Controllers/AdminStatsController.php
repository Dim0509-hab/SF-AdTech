<?php
namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Rejection;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Models\Conversion;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Models\User;


class AdminStatsController extends Controller
{
    public function index(Request $request)
{
    // Инициализация
    $countConversions = 0;
    // Валидация формата дат (если нужно)
    $request->validate([
        'from' => 'nullable|date_format:Y-m-d',
        'to' => 'nullable|date_format:Y-m-d',
    ]);

    // Получение значений
    $from = $request->input('from', now()->subDays(7)->format('Y-m-d'));
    $to = $request->input('to', now()->format('Y-m-d'));
    $status = $request->input('status', 'paid');



    // 1. Общий доход системы
    $totalRevenue = Click::join('offers', 'clicks.offer_id', '=', 'offers.id')
        ->sum('offers.revenue_per_click');

    // 2. Статистика переходов по дням (последние 30 дней)
    $clickStats = Click::selectRaw('DATE(created_at) as date, COUNT(*) as total_clicks')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->where('created_at', '>=', now()->subDays(30))
        ->get();

        // Новый расчёт: доход по офферам (топ‑5)
    $revenueByOffer = Conversion::select('offer_id',
            DB::raw('SUM(revenue) as total_revenue'))
        ->whereBetween('created_at', [$from, $to])
        ->where('status', $status)
        ->groupBy('offer_id')
        ->orderByDesc('total_revenue')
        ->limit(5)
        ->with('offer') // если нужна связь с моделью Offer
        ->get();

        // Новый расчёт: доход по пользователям (топ‑5)
    $revenueByUser = Conversion::select('user_id', DB::raw('SUM(revenue) as total_revenue'))
        ->whereBetween('created_at', [$from, $to])
        ->where('status', $status)
        ->groupBy('user_id')
        ->orderByDesc('total_revenue')
        ->limit(5)
        ->with('user') // если нужна связь с моделью User
        ->get();

    // 3. Уникальные выданные ссылки
    $uniqueLinks = Click::query()
    ->whereNotNull('link_hash') // если нужно учитывать только заполненные
    ->distinct('link_hash')
    ->count('link_hash');
    // 4. Последние отказы (10 записей)
    $rejections = Rejection::with(['webmaster', 'offer'])
        ->latest()
        ->paginate(10);

    // Затем расчёт (если нужны условия — добавляйте после)
    $countConversions = Conversion::whereBetween('created_at', [$from, $to])
        ->where('status', $status)
        ->count();

    // Теперь можно использовать
    $avgRevenue = $countConversions > 0
        ? $totalRevenue / $countConversions
        : 0;


    // 5. Базовая статистика из старого кода (можно оставить)
    $basicStats = [
        'total_users' => User::count(),
        'active_users' => User::where('active', true)->count(),
        'total_offers' => Offer::count(),
        'published_offers' => Offer::where('status', 'published')->count(),
    ];

    // 3. Новый расчёт: количество конверсий
    $countConversions = Conversion::whereBetween('created_at', [$from, $to])
        ->where('status', $status)
        ->count();

    return view('admin.revenue.index', compact(
        'totalRevenue',
        'clickStats',
        'uniqueLinks',
        'rejections',
        'basicStats',
        'avgRevenue',
        'revenueByOffer',
        'revenueByUser',
        'countConversions',
        'from',
        'to',
        'status'
    ));
}

public function export(Request $request)
{
    // 1. Получение фильтров (как в index())
    $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
    $to = $request->input('to', now()->format('Y-m-d'));
    $status = $request->input('status', 'paid');

    // 2. Получение данных для экспорта
    $conversions = Conversion::whereBetween('created_at', [$from, $to])
        ->where('status', $status)
        ->get();

    // 3. Формирование CSV
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="conversions_' . now()->format('Ymd_His') . '.csv"',
    ];

    $callback = function() use ($conversions) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID', 'User ID', 'Offer ID', 'Status', 'Revenue', 'Date']);

        foreach ($conversions as $conversion) {
            fputcsv($file, [
                $conversion->id,
                $conversion->user_id,
                $conversion->offer_id,
                $conversion->status,
                $conversion->revenue,
                $conversion->created_at,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


}
