<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClickController extends Controller
{
    /**
     * Обработка клика по реферальной ссылке
     */
    public function track(Request $request, $linkHash)
    {
        // Найти оффер по link_hash
        $offer = Offer::where('link_hash', $linkHash)->first();

        if (!$offer) {
            return response('Offer not found', 404);
        }

        // Определяем webmaster_id
        // Варианты: из авторизации, query-параметра, cookie
        $webmasterId = null;

        // Попробуем получить из авторизованного пользователя (если тестим в браузере)
        if ($request->user()) {
            $webmasterId = $request->user()->id;
        }

        // Или из параметра ?webmaster_id=123 (для внешних ссылок)
        if (!$webmasterId && $request->has('webmaster_id')) {
            $webmasterId = $request->input('webmaster_id');
        }

        // Если не удалось определить — можно записать как "органический" или отклонить
        if (!$webmasterId) {
            // Вариант 1: записать без webmaster_id (для аналитики)
            // Вариант 2: вернуть ошибку
            return redirect($offer->target_url); // просто редирект без учёта
        }

        // Проверить, подписан ли веб-мастер на этот оффер
        $subscription = DB::table('offer_webmaster')
            ->where('offer_id', $offer->id)
            ->where('webmaster_id', $webmasterId)
            ->first();

        if (!$subscription) {
            // Веб-мастер не подписан — можно отклонить или залогировать
            return response('Access denied: not subscribed', 403);
        }

        // Берём стоимость клика на момент перехода
        $costPerClick = $subscription->cost_per_click;

        // Создаём запись клика
        Click::create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmasterId,
            'link_hash' => $linkHash,
            'click_id' => Str::random(16),
            'cost' => $costPerClick, // ← ключевая строка: фиксируем ставку
            'user_agent' => $request->userAgent(),
            'count' => 1,
        ]);


        // Редирект на целевую страницу
        return redirect()->away($offer->target_url);
    }
}
