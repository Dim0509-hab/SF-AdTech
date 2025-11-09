<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'local'), // для разработки лучше local вместо production
    'debug' => (bool) env('APP_DEBUG', true), // true для разработки
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Yekaterinburg',
    'locale' => env('APP_LOCALE', 'ru'), // можно установить русский язык
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'ru'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
];
