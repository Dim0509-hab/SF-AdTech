<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdvertiserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Проверяем, авторизован ли пользователь
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Проверяем роль пользователя
        if (Auth::user()->role === 'advertiser') {
            return $next($request);
        }

        // Если роль не совпадает — запрещаем доступ
        abort(403, 'Доступ только для рекламодателей');
    }
}
