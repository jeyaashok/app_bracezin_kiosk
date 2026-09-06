<?php

return [
    'enabled' => env('API_CACHE_ENABLED', true),

    // Cache GET JSON API responses for a short period to reduce DB load.
    'ttl_seconds' => env('API_CACHE_TTL_SECONDS', 30),

    // Keep authenticated users cache-separated by user id in the key.
    'include_authenticated' => env('API_CACHE_INCLUDE_AUTHENTICATED', true),

    // Clear tracked GET cache entries after successful write calls.
    'invalidate_on_write' => env('API_CACHE_INVALIDATE_ON_WRITE', true),

    // If true, cache invalidation runs only for responses with status < 400.
    'invalidate_only_on_success' => env('API_CACHE_INVALIDATE_ONLY_ON_SUCCESS', true),

    // Invalidate cache entries only for the affected API resource scope.
    'granular_invalidation' => env('API_CACHE_GRANULAR_INVALIDATION', true),

    // If scope has no tracked keys, clear all tracked API cache keys as fallback.
    'invalidate_fallback_to_all' => env('API_CACHE_INVALIDATE_FALLBACK_TO_ALL', true),

    // Map write endpoints to additional scopes that should also be invalidated.
    'invalidation_scope_map' => [
        'api/role-map-permission' => ['role', 'permission'],
        'api/update-role-permissions' => ['role', 'permission'],
        'api/user-map-permission' => ['all-user', 'role', 'permission', 'staff', 'agent', 'vendor', 'customer'],
        'api/all-user-store-media' => ['all-user', 'media', 'vendor', 'customer', 'agent', 'staff'],
        'api/sale-order/*' => ['sale-order', 'sale-invoice', 'enquiry', 'quote-request'],
        'api/sale-invoice/*' => ['sale-invoice', 'sale-order'],
        'api/enquiry/*' => ['enquiry', 'enquiry-item'],
        'api/enquiry-item/*' => ['enquiry', 'enquiry-item'],
        'api/quote-request/*' => ['quote-request', 'quote-request-item'],
        'api/quote-request-item/*' => ['quote-request', 'quote-request-item'],
        'api/product/*' => ['product', 'category'],
        'api/category/*' => ['category', 'product'],
        'api/wallet/*' => ['wallet', 'wallet-balance'],
        'api/wallet-balance/*' => ['wallet-balance', 'wallet'],
        'api/menu-group/*' => ['menu-group', 'menu', 'sub-menu'],
        'api/menu/*' => ['menu', 'sub-menu', 'menu-group'],
        'api/sub-menu/*' => ['sub-menu', 'menu', 'menu-group'],
        'api/setting/*' => ['setting', 'financial_year'],
        'api/financial_year/*' => ['financial_year', 'setting'],
        'api/expense/*' => ['expense', 'expense_category'],
        'api/expense_category/*' => ['expense_category', 'expense'],
        'api/address/*' => ['address'],
        'api/payment-term/*' => ['payment-term'],
        'api/delivery-term/*' => ['delivery-term'],
        'api/comment/*' => ['comment'],
        'api/status/*' => ['status'],
        'api/v1/roles/*' => ['v1/roles', 'v1/permissions'],
        'api/v1/permissions/*' => ['v1/permissions', 'v1/roles'],
    ],

    // Write endpoints that should never trigger response-cache invalidation.
    'invalidation_ignored_paths' => [
        'api/login',
        'api/register',
        'api/forget-password',
        'api/reset-password',
        'api/change-password',
        'api/test-api',
    ],

    'key_prefix' => env('API_CACHE_KEY_PREFIX', 'api-response-cache:'),

    'ignored_paths' => [
        'api/v1/auth/*',
        'api/auth/*',
        'api/clear-cache',
        'api/*/clear-cache',
    ],
];