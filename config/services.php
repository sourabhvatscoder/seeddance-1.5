<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'seedance' => [
        'api_key' => env('SEEDANCE_API_KEY'),
        'base_url' => env('SEEDANCE_BASE_URL', 'https://api.seedance.com'),
        'generate_video_path' => env('SEEDANCE_GENERATE_VIDEO_PATH', '/v1/generate-video'),
        'timeout_seconds' => (int) env('SEEDANCE_TIMEOUT_SECONDS', 30),
        'retry_attempts' => (int) env('SEEDANCE_RETRY_ATTEMPTS', 3),
        'retry_sleep_ms' => (int) env('SEEDANCE_RETRY_SLEEP_MS', 500),
    ],

];
