<?php
namespace App\Http\Controllers;

use App\Models\Conversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdminRevenueController extends Controller
{
    // Промежуточное ПО: только админы
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Доступ запрещён');
            }
            return $next($request);
        });
    }

    /**
     * Страница статистики доходов
     */
    public function index(Request $request)
    {
        // Фильтры из запроса
        $from = $request->input('from') ?? now()->subMonth()->format('Y-m-d');
        $to = $request->input('to') ?? now()->format('Y-m-d');
        $status = $request->input('status') ?? 'paid';

        // Основной запрос
        $query = Conversion::query()
            ->whereBetween('conversion_date', [$from, $to])
            ->where('status', $status);

        // Агрегаты
        $totalRevenue = $query->sum('revenue');
        $countConversions = $query->count();
        $avgRevenue = $countConversions > 0 ? $totalRevenue / $countConversions : 0;

        // Группировка по офферам (топ‑5)
        $revenueByOffer = Conversion::select('offer_id',
         DB::raw('SUM(revenue) as total'))
            ->whereBetween('conversion_date', [$from, $to])
            ->where('status', $status)
            ->groupBy('offer_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Группировка по веб‑мастерам (топ‑5)
        $revenueByUser = Conversion::select('user_id',
        DB::raw('SUM(revenue) as total'))
            ->whereBetween('conversion_date', [$from, $to])
            ->where('status', $status)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.revenue.index', compact(
            'totalRevenue',
            'countConversions',
            'avgRevenue',
            'revenueByOffer',
            'revenueByUser',
            'from',
            'to',
            'status'
        ));
    }

    /**
     * Экспорт в CSV (опционально)
     */
    public function export(Request $request)
    {
        $data = Conversion::select([
                'conversion_date',
                'offer_id',
                'user_id',
                'revenue',
                'status'
            ])
            ->whereBetween('conversion_date', [
                $request->input('from'),
                $request->input('to')
            ])
            ->where('status', $request->input('status'))
            ->get();

        $filename = 'revenue_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Дата', 'Оффер', 'Веб‑мастер', 'Доход', 'Статус']);
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->conversion_date,
                    $row->offer_id,
                    $row->user_id,
                    $row->revenue,
                    $row->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
