<?php

return [
    'name' => env('APP_NAME', '茶文化'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Shanghai'),
    'locale' => env('APP_LOCALE', 'zh_CN'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),
];
