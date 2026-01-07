<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Current RMS API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Current RMS API integration. Supports API key
    | authentication.
    |
    */

    'api' => [
        'base_url' => env('CURRENT_RMS_API_BASE_URL', 'https://api.current-rms.com/api/v1'),
        'timeout' => env('CURRENT_RMS_API_TIMEOUT', 30),
        'connect_timeout' => env('CURRENT_RMS_API_CONNECT_TIMEOUT', 10),
        'verify_ssl' => env('CURRENT_RMS_API_VERIFY_SSL', true),
    ],

    'auth' => [
        // Authentication type: 'api_key' or 'oauth2'
        'type' => env('CURRENT_RMS_AUTH_TYPE', 'api_key'),

        // Subdomain is required for authentication
        'subdomain' => env('CURRENT_RMS_SUBDOMAIN'),

        'api_key' => [
            // API token (sent via X-AUTH-TOKEN header)
            'token' => env('CURRENT_RMS_API_TOKEN'),
        ],
    ],
];
