<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration allows your frontend app (Flutter Web / Mobile)
    | to make requests to this backend without CORS issues.
    |
    */

    'paths' => ['api/*'], // All API routes are allowed

    'allowed_methods' => ['*'], // Allow GET, POST, PUT, DELETE, PATCH, etc.

    'allowed_origins' => [
        'http://localhost:5173', // Flutter Web default dev server
        'http://127.0.0.1:5173',
        'http://localhost:8000', // Optional if testing locally
        '*'                      // Allow all origins during development
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Accept all headers from frontend

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // If you use cookies / auth later

];
