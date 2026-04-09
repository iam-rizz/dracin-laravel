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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Proxy Configuration
    |--------------------------------------------------------------------------
    |
    | Controls the HTTP proxy used for outbound API requests to the drama
    | content providers. Set PROXY_ENABLED=true and provide a proxy file
    | containing one proxy URI per line.
    |
    | Supported formats in proxy file:
    |   http://user:pass@host:port
    |   socks5://host:port
    |   host:port (treated as http)
    |
    */

    'proxy' => [
        'enabled' => env('PROXY_ENABLED', false),
        'file'    => env('PROXY_FILE', storage_path('proxy.txt')),
    ],

];
