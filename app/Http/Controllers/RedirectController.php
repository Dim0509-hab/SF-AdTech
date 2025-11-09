<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ClickRedirected;
use Illuminate\Http\Request;
use App\Models\Click;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class RedirectController extends Controller
{

    /**
 * Проверяет, является ли IP приватным (внутренним)
 */
private function isPrivateIp(string $ip): bool
{
    $privateRanges = [
        '127.0.0.0/8',    // localhost
        '10.0.0.0/8',     // private
        '172.16.0.0/12',  // private
        '192.168.0.0/16', // private
        'fc00::/7',       // unique local (IPv6)
        '::1/128',        // loopback IPv6
    ];

    foreach ($privateRanges as $range) {
        if ($this->ipInCIDR($ip, $range)) {
            return true;
        }
    }

    return false;
}

/**
 * Проверяет, входит ли IP в CIDR-диапазон
 */
private function ipInCIDR(string $ip, string $cidr): bool
{
    if (strpos($cidr, '/') === false) {
        return false;
    }

    [$subnet, $bits] = explode('/', $cidr, 2);
    $bits = (int) $bits;

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        return ($ip & $mask) === ($subnet & $mask);
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $ip = inet_pton($ip);
        $subnet = inet_pton($subnet);
        $mask = str_repeat("\xFF", (int) ($bits / 8)) .
                str_repeat("\x00", 16 - (int) ($bits / 8));
        if ($bits % 8) {
            $mask[$bits >> 3] = str_repeat(chr(0xFF << (8 - ($bits & 7))), 1);
        }
        return ($ip & $mask) === ($subnet & $mask);
    }

    return false;
}

    private function parseUserAgent(string $ua): array
    {
        $result = [
            'device_type' => 'desktop',
            'browser' => 'unknown',
            'os' => 'unknown',
        ];

        // === ОПРЕДЕЛЕНИЕ УСТРОЙСТВА ===
        if (preg_match('/Mobile|Android.+Mobile|iPhone|iPod/i', $ua)) {
            $result['device_type'] = 'mobile';
        } elseif (preg_match('/iPad|Android.+?(?=Tablet)|Kindle/i', $ua)) {
            $result['device_type'] = 'tablet';
        } // иначе остаётся 'desktop'

        // === ОПРЕДЕЛЕНИЕ БРАУЗЕРА ===
        if (preg_match('/Edg\/([\d.]+)/i', $ua, $m)) {
            $result['browser'] = 'Edge';
        } elseif (preg_match('/Chrome\/([\d.]+)/i', $ua, $m) && !preg_match('/Edg|Opera|OPR/', $ua)) {
            $result['browser'] = 'Chrome';
        } elseif (preg_match('/Firefox\/([\d.]+)/i', $ua, $m)) {
            $result['browser'] = 'Firefox';
        } elseif (preg_match('/Safari\/([\d.]+)/i', $ua) && !preg_match('/Chrome|Edg/', $ua)) {
            $result['browser'] = 'Safari';
        } elseif (preg_match('/Opera|OPR\/([\d.]+)/i', $ua)) {
            $result['browser'] = 'Opera';
        } elseif (preg_match('/MSIE\s([\d.]+)|Trident\/.+?rv:([\d.]+)/i', $ua)) {
            $result['browser'] = 'Internet Explorer';
        }

        // === ОПРЕДЕЛЕНИЕ ОС ===
        if (preg_match('/Windows NT 10\.0/i', $ua)) {
            $result['os'] = 'Windows 10';
        } elseif (preg_match('/Windows NT 6\.3/i', $ua)) {
            $result['os'] = 'Windows 8.1';
        } elseif (preg_match('/Mac OS X.*?FxiOS|iPhone OS|CriOS/i', $ua)) {
            $result['os'] = 'iOS';
        } elseif (preg_match('/Mac OS X/i', $ua)) {
            $result['os'] = 'macOS';
        } elseif (preg_match('/Android/i', $ua)) {
            $result['os'] = 'Android';
        } elseif (preg_match('/Linux/i', $ua) && !preg_match('/Android/i', $ua)) {
            $result['os'] = 'Linux';
        } elseif (preg_match('/CrOS/i', $ua)) {
            $result['os'] = 'Chrome OS';
        }

        return $result;
    }

            /**
     * Определяет геолокацию по IP через ipapi.co
     * Кэширует результат на 30 дней
     *
     * @param string $ip
     * @return array
     */
    private function geolocate(string $ip): array
    {
        // Проверяем через наш метод — без FILTER_FLAG_PRIVATE
        if ($this->isPrivateIp($ip)) {
            return [];
        }

        return Cache::remember("geoip.{$ip}", now()->addDays(30), function () use ($ip) {
            try {
                $url = "https://ipapi.co/{$ip}/json/";
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 3,
                        'user_agent' => 'Laravel GeoIP Client'
                    ]
                ]);

                $response = file_get_contents($url, false, $context);

                if ($response === false) {
                    Log::warning('ipapi.co: Нет ответа', ['ip' => $ip]);
                    return [];
                }

                $data = json_decode($response, true);

                if (isset($data['error'])) {
                    Log::warning('ipapi.co: Ошибка в ответе', ['ip' => $ip, 'error' => $data['error']]);
                    return [];
                }

                return [
                    'country' => $data['country_name'] ?? null,
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'postal' => $data['postal'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                ];
            } catch (\Exception $e) {
                Log::warning('Геолокация не удалась', ['ip' => $ip, 'error' => $e->getMessage()]);
                return [];
            }
        });
    }

        private function isBot(string $ua): bool
    {
        $botPatterns = [
            'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'bingbot',
            'yandex', 'duckduckbot', 'facebookexternalhit', 'linkedinbot',
            'twitterbot', 'pinterest', 'slackbot', 'whatsapp', 'telegrambot'
        ];

        $uaLower = strtolower($ua);

        foreach ($botPatterns as $pattern) {
            if (strpos($uaLower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

public function redirect(Request $request, )
{

     $token = $request->query('token');
    $offerId = $request->query('offer_id');

    if (!$token) abort(400, 'Token is required');
    if (strlen($token) > 255) abort(404);
    if (!$offerId) abort(400, 'Offer ID is required');



    // === 2. Проверка подписи токена ===
    $decoded = base64_decode($token);
    if (!preg_match('/^(\d+)_(\d+)\.([a-f0-9]{64})$/', $decoded, $m)) {
        abort(404);
    }
    [$offerId, $webmasterId, $sig] = [$m[1], $m[2], $m[3]];
    $validSig = hash_hmac('sha256', "{$offerId}_{$webmasterId}", config('app.key'));
    if (!hash_equals($validSig, $sig)) abort(404);

    // === 3. Проверка на бота ===
    if ($this->isBot($request->userAgent())) {
        DB::table('rejections')->insert([
            'offer_id' => $offerId,
            'webmaster_id' => $webmasterId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'reason' => 'Bot traffic',
            'attempted_at' => now(),
        ]);
        abort(404);
    }

    // === 4. Защита от дубля: IP + offer за 10 минут ===
    $duplicate = Click::where('ip', $request->ip())
        ->where('offer_id', $offerId)
        ->where('clicked_at', '>', now()->subMinutes(10))
        ->exists();

    if ($duplicate) {
        DB::table('rejections')->insert([
            'offer_id' => $offerId,
            'webmaster_id' => $webmasterId,
            'ip' => $request->ip(),
            'reason' => 'Duplicate click (IP + offer)',
            'attempted_at' => now(),
        ]);
        abort(404);
    }

    // === 5. Поиск клика с блокировкой ===
    $click = Click::where('click_token', $token)->lockForUpdate()->first();

    if (!$click) {
        $click = Click::create([
            'offer_id' => $offerId,
            'webmaster_id' => $webmasterId,
            'click_token' => $token,
            'referer' => $request->headers->get('referer'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'cost' => 0,
            'redirected' => false,
            'clicked_at' => now(),
        ]);
    } else {
        if ($click->redirected) {
            abort(404);
        }
    }

    // === 6. Гео и UA (с защитой от падений) ===
    try {
        $geo = $this->geolocate($request->ip());
    } catch (\Exception $e) {
        \Log::warning('Geo failed', ['ip' => $request->ip(), 'error' => $e->getMessage()]);
        $geo = [];
    }

    try {
        $ua = $this->parseUserAgent($request->userAgent());
    } catch (\Exception $e) {
        \Log::warning('UA parse failed', ['ua' => $request->userAgent()]);
        $ua = ['device_type' => 'unknown', 'browser' => 'unknown', 'os' => 'unknown'];
    }

    // === 7. Проверка оффера ===
    $offer = $click->offer;
    if (!$offer || !$offer->is_active) {
        abort(404);
    }

    // === 8. Проверка подписки (с кэшированием) ===
    $cacheKey = "offer_wm_{$offerId}_{$webmasterId}";
    $sub = Cache::remember($cacheKey, now()->addHour(), function () use ($offerId, $webmasterId) {
        return DB::table('offer_webmaster')
            ->where('offer_id', $offerId)
            ->where('webmaster_id', $webmasterId)
            ->select('agreed_price')
            ->first();
    });

    if (!$sub) {
        DB::table('rejections')->insert([
            'offer_id' => $offerId,
            'webmaster_id' => $webmasterId,
            'ip' => $request->ip(),
            'referer' => $request->headers->get('referer'),
            'reason' => 'No subscription',
            'attempted_at' => now(),
        ]);
        abort(404);
    }

    // === 9. Обновление клика одним saveQuietly() ===
    $click->fill([
        'country' => $geo['country'] ?? null,
        'region' => $geo['region'] ?? null,
        'city' => $geo['city'] ?? null,
        'postal_code' => $geo['postal'] ?? null,
        'latitude' => $geo['latitude'] ?? null,
        'longitude' => $geo['longitude'] ?? null,
        'device_type' => $ua['device_type'],
        'browser' => $ua['browser'],
        'os' => $ua['os'],
        'cost' => $sub->agreed_price ?? $offer->price,
        'redirected' => true,
        'redirected_at' => now(),
    ])->saveQuietly();

    // === 10. Событие в очередь ===
    ClickRedirected::dispatch($click);

    // === 11. Редирект ===
    return redirect()->away($offer->target_url);
}

/**
 * Логирует отказ в переходе
 *
 * @param int $webmasterId    ID вебмастера (из таблицы users)
 * @param int $offerId        ID оффера
 * @param string $reason      Причина отказа (например, 'bot_ua', 'geo_mismatch')
 * @param string $ip          IP-адрес (можно получить через $request->ip())
 * @param array $context      Дополнительные данные (например, страна, устройство)
 * @return void
 */
function logRejection($webmasterId, $offerId, $reason, $ip, $context = [])
{
    // Подключаем DB, если не Laravel (в Laravel можно использовать DB::)
    $pdo = DB::getPdo(); // Если в Laravel

    $sql = "INSERT INTO rejections
            (webmaster_id, offer_id, reason, is_suspicious, ip, context, created_at)
            VALUES
            (?, ?, ?, ?, ?, ?, NOW())";

    $isSuspicious = in_array($reason, [
        'bot_ua',           // Подозрительный браузер
        'click_spam',       // Слишком много кликов
        'fraud_rate_limit', // Накрутка
        'blacklisted_ip',   // IP в чёрном списке
        'invalid_referer'   // Поддельный реферер
    ]) ? 1 : 0;

    $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE);

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $webmasterId,
        $offerId,
        $reason,
        $isSuspicious,
        $ip,
        $contextJson
    ]);
}


// В RedirectController

private function buildRedirectUrl($offer, $click, $context = [])
{
    $url = $offer->target_url;

    $replacements = [
        '{webmaster_id}' => $click->webmaster_id,
        '{offer_id}'     => $click->offer_id,
        '{click_id}'     => $click->id,
        '{click_token}'  => $click->click_token,
        '{ip}'           => $click->ip,
        '{country}'      => $click->country ?? 'unknown',
        '{device}'       => $click->device_type ?? 'desktop',
    ];

    // Заменяем все токены
    $url = str_replace(
        array_keys($replacements),
        array_values($replacements),
        $url
    );

    // Добавляем UTM-метки, если их нет
    if (!Str::contains($url, 'utm_source')) {
        $utm = http_build_query([
            'utm_source' => 'sf_adtech',
            'utm_medium' => 'cpa',
            'utm_campaign' => "offer_{$offer->id}",
            'utm_content' => "wm_{$click->webmaster_id}",
        ]);
        $glue = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
        $url .= $glue . $utm;
    }

    return $url;
}

private function validateRedirectUrl($url)
{
    // 1. Проверка на пустоту
    if (! $url || ! is_string($url)) {
        Log::warning('target_url пуст или не строка', ['url' => $url]);
        return false;
    }

    // 2. Удаление лишних пробелов
    $url = trim($url);

    // 3. Проверка длины (защита от переполнения)
    if (strlen($url) > 2048) {
        Log::warning('target_url слишком длинный', ['length' => strlen($url)]);
        return false;
    }

    // 4. Парсинг URL
    $parsed = @parse_url($url);
    if (! $parsed || ! isset($parsed['host'])) {
        Log::warning('Не удалось распарсить URL', ['url' => $url]);
        return false;
    }

    // 5. Проверка схемы
    $scheme = $parsed['scheme'] ?? '';
    if (! in_array(strtolower($scheme), ['http', 'https'])) {
        Log::warning('Неподдерживаемая схема', ['scheme' => $scheme]);
        return false;
    }

    // 6. Проверка домена
    $host = $parsed['host'];

    // Запрещённые символы в домене
    if (! preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)*$/i', $host)) {
        Log::warning('Некорректный формат домена', ['host' => $host]);
        return false;
    }

    // 7. Проверка на localhost / внутренние IP
    $forbiddenHosts = ['localhost', '127.0.0.1', '0.0.0.0', '[::1]'];
    if (in_array(strtolower($host), $forbiddenHosts)) {
        Log::warning('Запрещённый хост', ['host' => $host]);
        return false;
    }

    // 8. Проверка по черному списку (можно вынести в настройки)
    $blockedDomains = [
        'malware.com',
        'fake-offer.net',
        'phishing.ru',
        // ... или подгружать из базы: BlockedDomain::pluck('domain')->toArray()
    ];

    if (in_array(strtolower($host), $blockedDomains)) {
        Log::warning('Домен в чёрном списке', ['host' => $host]);
        return false;
    }

    // 9. Опционально: проверка доступности домена (на продакшене — аккуратно!)
    // Раскомментируйте, если хотите проверять reachability
    /*
    try {
        $response = Http::timeout(5)->head($url);
        if (! $response->successful() && ! $response->redirect()) {
            Log::warning('URL недоступен', ['url' => $url, 'status' => $response->status()]);
            return false;
        }
    } catch (\Exception $e) {
        Log::warning('Ошибка при проверке доступности URL', ['url' => $url, 'error' => $e->getMessage()]);
        return false;
    }
    */

    return true;
}

/**
     * Определить тип устройства
     */
    private function detectDevice($request)
    {
        $userAgent = $request->userAgent();

        if (Str::contains($userAgent, ['Mobile', 'Android', 'iPhone', 'iPad'])) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Простейшее определение страны по IP (заглушка)
     */
    private function guessCountry($ip)
    {
        // Здесь можно подключить MaxMind, IPGeo и т.п.
        // Для примера — заглушка
        return 'RU'; // или использовать сервис
    }

    /**
     * Нужно ли использовать deeplink?
     */
    private function shouldUseDeeplink($click, $offer)
    {
        return $click->device_type === 'mobile'
            && $offer->deeplink
            && $this->isValidDeeplink($offer->deeplink);
    }

    /**
     * Проверить валидность deeplink
     */
    private function isValidDeeplink($deeplink)
    {
        $parsed = @parse_url($deeplink);
        return $parsed && isset($parsed['scheme']) && $parsed['scheme'] !== 'http' && $parsed['scheme'] !== 'https';
    }

    /**
     * Построить Android Intent
     */
    private function buildAndroidIntent($deeplink, $fallbackUrl)
    {
        parse_str(parse_url($deeplink, PHP_URL_QUERY), $query);
        $package = $query['package'] ?? 'com.default.app';
        $component = $package . '/.MainActivity';

        return "intent://"
            . ltrim(parse_url($deeplink, PHP_URL_PATH), '/') . "#Intent;"
            . "package=" . $package . ";"
            . "S.browser_fallback_url=" . urlencode($fallbackUrl) . ";"
            . "scheme=" . parse_url($deeplink, PHP_URL_SCHEME) . ";"
            . "action=android.intent.action.VIEW;"
            . "category=android.intent.category.BROWSABLE;"
            . "component=" . $component . ";"
            . "end";
    }

    /**
     * Извлечь package из deeplink (опционально)
     */
    private function extractPackageFromDeeplink($deeplink)
    {
        parse_str(parse_url($deeplink, PHP_URL_QUERY), $query);
        return $query['package'] ?? 'com.default.app';
    }

    public function handle(Request $request, $encodedData)
    {
        // 🔹 1. Валидация формата токена
        if (!is_string($encodedData) || !preg_match('/^[a-zA-Z0-9+\/=]+$/', $encodedData)) {
            Log::warning('Некорректный формат токена', ['data' => $encodedData]);
            abort(400, 'Invalid token format');
        }

        // 🔹 2. Декодируем из base64 (без строгой проверки)
    $decoded = base64_decode($encodedData); // Убрали $strict = true
    if ($decoded === false) {
        Log::warning('Невозможно декодировать base64', ['data' => $encodedData]);
        abort(400, 'Invalid base64');
    }


        // 🔹 3. Извлекаем click_id
    parse_str($decoded, $output);
    $clickId = $output['click_id'] ?? null;

    Log::info('Раскодированная строка', ['decoded' => $decoded]);
    Log::info('Извлечённые данные', ['output' => $output, 'click_id_raw' => $clickId]);

    if (!is_numeric($clickId)) {
        Log::warning('click_id не найден или не числовой', ['decoded' => $decoded, 'output' => $output]);
        abort(400, 'click_id required');
    }

    // Приводим к целому числу
    $clickId = (int) $clickId;
    Log::info('Ищем клик по ID', ['click_id' => $clickId, 'type' => gettype($clickId)]);

    // 🔹 4. Ищем клик по ID
    $click = Click::find($clickId);

    if (!$click) {
        Log::warning('Клик не найден в БД', ['click_id' => $clickId]);
        // Дополнительно: проверим, есть ли вообще клики
        $totalClicks = \App\Models\Click::count();
        Log::warning('Всего кликов в БД', ['count' => $totalClicks]);
        abort(404, 'Click not found');
    }

    Log::info('Клик найден', ['click_id' => $click->id, 'offer_id' => $click->offer_id]);


        // 🔹 5. Проверяем, активен ли оффер
        $offer = $click->offer ?? Offer::find($click->offer_id);

    if (!$offer) {
        Log::warning('Оффер не найден', ['offer_id' => $click->offer_id]);
        abort(404, 'Offer not found');
    }

    // 🔹 6. Дополняем данные, если ещё не заполнены
    if (!$click->user_agent) {
        $click->fill([
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'device_type' => $this->detectDevice($request),
            'country' => $this->guessCountry($request->ip()),
        ])->saveQuietly();
    }

    // 🔹 7. Строим финальный URL
    $finalUrl = $this->buildRedirectUrl($offer, $click);
    if (!$finalUrl) {
        Log::error('buildRedirectUrl вернул пустой URL', ['offer_id' => $offer->id, 'click_id' => $click->id]);
        abort(500, 'Redirect URL generation failed');
    }

    if (!$this->validateRedirectUrl($finalUrl)) {
        Log::error('Некорректный target_url', ['url' => $finalUrl, 'offer_id' => $offer->id]);
        abort(500, 'Invalid redirect URL');
    }

    // 🔹 8. Проверяем, нужен ли deeplink
    if ($this->shouldUseDeeplink($click, $offer) && $offer->deeplink) {
        $intent = $this->buildAndroidIntent($offer->deeplink, $finalUrl);
        return redirect()->away($intent);
    }

    // 🔹 9. Дефолтный веб-редирект
    return redirect()->away($finalUrl);
}



}
