<?php

$frontendUrls = array_filter(array_map(
    static fn (string $url) => rtrim(trim($url), '/'),
    explode(',', (string) env('FRONTEND_URL', ''))
));

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique([
        'http://localhost:5173',
        'http://localhost:5174',
        ...$frontendUrls,
    ])),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    // L'authentification utilise des Bearer tokens et non des cookies cross-domain.
    'supports_credentials' => false,
];
