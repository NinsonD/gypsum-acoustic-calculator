<?php

declare(strict_types=1);

return [
    'name' => 'Al Namariq Gypsum & Acoustic Engineering Systems',
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim((string) env('APP_URL', ''), '/'),
    'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'whatsapp_number' => env('WHATSAPP_NUMBER', ''),
    'analytics_id' => env('GOOGLE_ANALYTICS_ID', ''),
    'db' => [
        'host' => env('DB_HOST', 'localhost'),
        'name' => env('DB_NAME', ''),
        'user' => env('DB_USER', ''),
        'pass' => env('DB_PASS', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
    ],
];
