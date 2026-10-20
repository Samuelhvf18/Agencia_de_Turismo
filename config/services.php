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

    // Pasarela de pago simulada (Módulo 06). El webhook valida la firma HMAC con este secreto.
    'pasarela' => [
        'secreto' => env('PASARELA_SECRETO'),
        'webhook_http' => (bool) env('PASARELA_WEBHOOK_HTTP', false),
    ],

    // n8n: webhooks de notificaciones y chatbot; el token protege los endpoints que n8n llama.
    'n8n' => [
        'webhook_notificaciones' => env('N8N_WEBHOOK_NOTIFICACIONES'),
        'webhook_chatbot' => env('N8N_WEBHOOK_CHATBOT'),
        'token' => env('N8N_TOKEN'),
    ],

];
