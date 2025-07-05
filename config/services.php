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

    'resend' => [
        'key' => env('RESEND_KEY'),
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
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para webhooks da aplicação
    |
    */
    'webhook' => [
        // Chave de API para autenticação de webhooks (opcional, mas recomendado)
        'api_key' => env('WEBHOOK_API_KEY', 'sua_chave_secreta_aqui'),
        
        // IPs permitidos (opcional, para restrição de acesso)
        'allowed_ips' => env('WEBHOOK_ALLOWED_IPS') ? 
            explode(',', env('WEBHOOK_ALLOWED_IPS')) : null,
            
        // Habilitar/desabilitar verificação de IP
        'enable_ip_restriction' => env('WEBHOOK_ENABLE_IP_RESTRICTION', false),
    ],

];
