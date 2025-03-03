<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Chỉ định API endpoints
    'allowed_methods' => ['*'],
    'allowed_origins' => [
    'http://localhost:5173',
    'http://127.0.0.1:5173',
    'http://localhost:3000', // Thêm cho Create React App
    'http://127.0.0.1:3000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Nếu dùng Sanctum, đặt là true
];
