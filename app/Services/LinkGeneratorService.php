<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class LinkGeneratorService
{
    /**
     * Генерирует отслеживаемую и безопасную партнерскую ссылку
     *
     * @param int $offerId
     * @param int $webmasterId
     * @param array $utm
     * @param bool $secure
     * @return string
     */
    public function generate(int $offerId, int $webmasterId, array $utm = [], bool $secure = true): string
    {
        $baseUrl = rtrim(config('app.url'), '/') . '/r/';

        $token = $secure
            ? $this->generateSignedToken($offerId, $webmasterId)
            : "offer_{$offerId}_wm_{$webmasterId}";

        $link = $baseUrl . $token;

        if (!empty($utm)) {
            $link = $this->appendUtm($link, $utm);
        }

        return $link;
    }

    /**
     * Создаёт подписанный токен: base64(offerId_webmasterId.signature)
     */
    private function generateSignedToken(int $offerId, int $webmasterId): string
    {
        $payload = "{$offerId}_{$webmasterId}";
        $signature = hash_hmac('sha256', $payload, config('app.key'));
        $signed = "{$payload}.{$signature}";
        return base64_encode($signed);
    }

    /**
     * Добавляет UTM-метки к ссылке
     */
    private function appendUtm(string $url, array $utm): string
    {
        $params = array_filter($utm); // убираем пустые
        if (empty($params)) return $url;

        $glue = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
        return $url . $glue . http_build_query($params);
    }
}
