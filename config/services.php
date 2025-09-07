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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [    
        'client_id' => env('GOOGLE_CLIENT_ID',"406055892745-lmbl0g3f0k4m3ihnnd74md1avltv00ge.apps.googleusercontent.com"),  
        'client_secret' => env('GOOGLE_CLIENT_SECRET',"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"),  
        'redirect' => env('GOOGLE_REDIRECT_URI',"http://127.0.0.1:8000/auth/google/callback") 
    ],

];
