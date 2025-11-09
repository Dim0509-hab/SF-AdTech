<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Conversion;
use App\Models\Offer;
use App\Models\Rejection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatsController extends Controller
{
    public function index(Request $request)
    {
        // Валидация
        $request->validate([
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|in:paid,pending,rejected',
        ]);

        // Фильтры
        $from = $request->input('from', now()->subDays(7)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $status = $request->input('status', 'paid');

        // 1. Доход за период — через конверсии (реальные деньги)
        $totalRevenue = Conversion::whereBetween('created_at', [$from, $to])
            ->where('status', $status)
            ->sum('revenue');

        // 2. Количество конверсий за период
        $countConversions = Conversion::whereBetween('created_at', [$from, $to])
            ->where('status', $status)
            ->count();

        // 3. Средний чек
        $avgRevenue = $countConversions > 0 ? $totalRevenue / $countConversions : 0;

        // 4. Доход по офферам (топ-5)
        $revenueByOffer = Conversion::select(['offer_id', DB::raw('SUM(revenue) as total_revenue')])
            ->whereBetween('created_at', [$from, $to])
            ->where('status', $status)
            ->groupBy('offer_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('offer:id,name') // только нужные поля
            ->get();

        // 5. Доход по пользователям (топ-5)
        $revenueByUser = Conversion::select(columns: ['user_id', DB::raw('SUM(revenue) as total_revenue')])
            ->whereBetween('created_at', [$from, $to])
            ->where('status', $status)
            ->groupBy('user_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('user:id,name,email')
            ->get();

        // 6. Статистика по кликам (последние 30 дней)
        $clickStats = Click::selectRaw('DATE(created_at) as date, COUNT(*) as total_clicks')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $uniqueLinks = Click::select(['offer_id', 'webmaster_id'])
            ->distinct()
            ->count();


        // 8. Последние отказы
        $rejections = Rejection::with(['webmaster:id,name', 'offer:id,name'])
            ->latest()
            ->paginate(10);

        // 9. Базовая статистика
        $basicStats = [
            'total_users' => User::count(),
            'active_users' => User::where('active', true)->count(),
            'total_offers' => Offer::count(),
            'published_offers' => Offer::where('status', 'published')->count(),
        ];

        return view('admin.revenue.index', compact(
            'totalRevenue',
            'countConversions',
            'avgRevenue',
            'revenueByOffer',
            'revenueByUser',
            'clickStats',
            'uniqueLinks',
            'rejections',
            'basicStats',
            'from',
            'to',
            'status'
        ));
    }

    public function export(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $status = $request->input('status', 'paid');

        $conversions = Conversion::with(['user:id,name,email', 'offer:id,name'])
            ->whereBetween('created_at', [$from, $to])
            ->where('status', $status)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="conversions_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($conversions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Пользователь', 'Оффер', 'Статус', 'Доход', 'Дата']);

            foreach ($conversions as $conversion) {
                fputcsv($file, [
                    $conversion->id,
                    $conversion->user?->name ?? '—',
                    $conversion->offer?->name ?? '—',
                    $conversion->status,
                    number_format($conversion->revenue, 2),
                    $conversion->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
