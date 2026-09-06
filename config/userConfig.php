<?php

return [
    'default_user_type' => 'customer',
    'default_user_password' => 'secret',

    'api_authenticatable' => [
        'superadmin' => true,
        'admin' => true,
        'agent' => true,
        'staff' => true,
        'company' => true,
        'customer' => true,
        'display' => false,
    ],

    'web_authenticatable' => [
        'superadmin' => true,
        'admin' => true,
        'agent' => true,
        'staff' => true,
        'company' => true,
        'customer' => true,
        'display' => false,
    ],

    'mobile_authenticatable' => [
        'superadmin' => false,
        'admin' => false,
        'agent' => true,
        'staff' => true,
        'company' => true,
        'customer' => true,
        'display' => false,
    ],

    'display_authenticatable' => [
        'superadmin' => false,
        'admin' => false,
        'agent' => false,
        'staff' => false,
        'company' => false,
        'customer' => false,
        'display' => true,
    ],

    'device_authenticatable' => [
        'computer' => [
            'superadmin' => true,
            'admin' => true,
            'agent' => true,
            'staff' => true,
            'company' => true,
            'customer' => true,
            'display' => false,
        ],
        'android_mobile' => [
            'superadmin' => false,
            'admin' => false,
            'agent' => true,
            'staff' => true,
            'company' => true,
            'customer' => true,
            'display' => false,
        ],
        'ios_mobile' => [
            'superadmin' => false,
            'admin' => false,
            'agent' => true,
            'staff' => true,
            'company' => true,
            'customer' => true,
            'display' => false,
        ],
        'display_panel' => [
            'superadmin' => false,
            'admin' => false,
            'agent' => false,
            'staff' => false,
            'company' => false,
            'customer' => false,
            'display' => true,
        ],
    ],

    'providers' => [
        // Roles Permission Service Provider
        PermissionServiceProvider::class,
        // Flash Message Service Provider
        FlashServiceProvider::class,
        // Excel Services
        ExcelServiceProvider::class,
        // JWT Auth Service Provider
        LaravelServiceProvider::class,
        // Cache Provider
        Service::class,
    ],

    'aliases' => [
        'Excel' => Excel::class,
        'JWTAuth' => JWTAuth::class,
        'JWTFactory' => JWTFactory::class,
    ],
];
