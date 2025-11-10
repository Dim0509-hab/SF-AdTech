<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RealRequestTimer
{
    /**
     * Обработка запроса
     */
    public function handle(Request $request, Closure $next)
    {
        // Убедимся, что время старта установлено
        $request->attributes->set('start_time', microtime(true));

        return $next($request);
    }

    /**
     * Выполняется в конце запроса
     */
    public function terminate($request, $response)
    {
        $start = $request->attributes->get('start_time');

        // На всякий случай — если start_time не установлен
        if (!$start) {
            Log::warning('⚠️ RealRequestTimer: start_time не установлен', [
                'url' => $request->url(),
                'method' => $request->method(),
            ]);
            return;
        }

        $duration = microtime(true) - $start;
        $timeMs = round($duration * 1000, 2);
        // 🔥 ДОБАВИМ ОТЛАДКУ: что за путь?
    \Log::debug('🔍 Путь запроса', [
        'path' => $request->path(),
        'full_url' => $request->url(),
        'segments' => $request->segments(),
    ]);

        // Логируем ВСЕ запросы в laravel.log — для отладки
        Log::debug('⏱️ Запрос завершён', [
            'path' => $request->path(),
            'method' => $request->method(),
            'duration_ms' => $timeMs,
            'ip' => $request->ip(),
            'user_id' => Auth::id() ?? 'guest',
        ]);

        // Если это админка — пишем в отдельный лог
        if (str_starts_with($request->path(), 'admin')) {
            Log::channel('admin')->info('🎯 Админ-доступ', [
                'method' => $request->method(),
                'url' => $request->url(),
                'duration_ms' => $timeMs,
                'ip' => $request->ip(),
                'user' => optional(Auth::user())->name ?? 'guest',
                'user_id' => Auth::id(),
            ]);
        }

        // Медленные запросы — в error
        if ($timeMs > 1000) {
            Log::error('🔴 Очень медленный запрос', [
                'url' => $request->url(),
                'duration_ms' => $timeMs,
                'ip' => $request->ip(),
            ]);
        }
    }
}
