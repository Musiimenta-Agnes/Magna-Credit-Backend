<?php


return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],   // tighten this in production
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];








// return [
//     'paths' => ['api/*'],

//     'allowed_methods' => ['*'],

//     'allowed_origins' => ['*'],  // or restrict to: ['http://localhost:58778']

//     'allowed_origins_patterns' => [],

//     'allowed_headers' => ['*'],

//     'exposed_headers' => [],

//     'max_age' => 0,

//     'supports_credentials' => false,
// ];