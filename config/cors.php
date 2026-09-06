<?php

return [

    /*
        |--------------------------------------------------------------------------
        | Laravel CORS
        |--------------------------------------------------------------------------
        |
        | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
        | to accept any value.
        |
    */
    'exposedHeaders' => ['Authorization'],

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
