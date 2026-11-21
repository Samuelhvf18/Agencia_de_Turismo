<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
    'notificaciones' => [
        'url' => env('NOTIFICACIONES_URL'),
        'token' => env('NOTIFICACIONES_TOKEN'),
    ],

    /* Integraciones externas. */

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

    // Firma HMAC de la pasarela.
    'pasarela' => [
        'secreto' => env('PASARELA_SECRETO'),
        'webhook_http' => (bool) env('PASARELA_WEBHOOK_HTTP', false),
    ],

    // Webhooks y token de n8n.
    'n8n' => [
        'webhook_notificaciones' => env('N8N_WEBHOOK_NOTIFICACIONES'),
        'webhook_chatbot' => env('N8N_WEBHOOK_CHATBOT'),
        'token' => env('N8N_TOKEN'),
    ],

];
