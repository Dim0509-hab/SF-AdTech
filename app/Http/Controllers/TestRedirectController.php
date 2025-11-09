<?php


namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestRedirectController extends Controller
{
    public function test(Request $request)
    {
        // Валидация
        $request->validate([
            'offer_id' => 'required|exists:offers,id',
            'webmaster_id' => 'nullable|integer',
            'ip' => 'nullable|ip',
            'url' => 'nullable|url', // целевой URL для проверки
        ]);

        $offerId = $request->input('offer_id');
        $webmasterId = $request->input('webmaster_id', 1);
        $testIp = $request->input('ip', '8.8.8.8');
        $testUrl = $request->input('url', 'https://example.com/offer');

        // Подменяем IP и заголовки для теста
        $request->headers->set('User-Agent', 'TestBot/1.0 (+https://example.com)');
        $request->headers->set('Referer', 'https://google.com/search?q=test');

        // Подменяем IP через прокси-метод Laravel
        $request->server->set('HTTP_X_FORWARDED_FOR', $testIp);


        // Имитируем реальный IP
        $realIp = $testIp;

        // Поиск или создание оффера (для теста)
        $offer = Offer::find($offerId);
        if (!$offer) {
            return response()->json(['error' => 'Offer not found'], 404);
        }

        // Генерация токена
        $token = md5(uniqid('test_', true));

        // === Логика из RedirectController ===
        DB::beginTransaction();

        try {
            // Поиск клика по токену
            $click = Click::where('click_token', $token)->lockForUpdate()->first();

            if (!$click) {
                $click = Click::create([
                    'offer_id' => $offerId,
                    'webmaster_id' => $webmasterId,
                    'click_token' => $token,
                    'referer' => $request->headers->get('referer'),
                    'ip' => $realIp,
                    'user_agent' => $request->userAgent(),
                    'cost' => $offer->payout,
                    'redirected' => false,
                    'clicked_at' => now(),
                    'redirect_attempts' => 0,
                    'country' => 'US',
                    'region' => 'California',
                    'city' => 'Los Angeles',
                    'device_type' => 'desktop',
                    'browser' => 'Chrome',
                    'os' => 'Windows',
                ]);
            } else {
                if ($click->redirected) {
                    DB::commit();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Click already redirected',
                        'click_id' => $click->id
                    ], 400);
                }
            }

            // Формируем URL
            $finalUrl = $testUrl . '?sub_id=' . $click->id;

            // Фиксируем редирект
            $click->update([
                'redirected' => true,
                'redirected_at' => now(),
                'final_url' => $finalUrl,
                'redirect_attempts' => DB::raw('redirect_attempts + 1')
            ]);

            DB::commit();

            // 🔍 Возвращаем результат (не редиректим в тесте)
            return response()->json([
                'status' => 'success',
                'message' => 'Test redirect simulated',
                'click' => $click,
                'redirect_to' => $finalUrl,
                'test_summary' => [
                    'offer_id' => $offerId,
                    'webmaster_id' => $webmasterId,
                    'ip' => $realIp,
                    'token' => $token,
                    'user_agent' => $request->userAgent(),
                    'referer' => $request->headers->get('referer'),
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Test redirect failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
