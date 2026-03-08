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

    'ai' => [
        'provider' => env('AI_PROVIDER')
            ?: (env('OPENAI_API_KEY')
                ? 'openai'
                : (env('ANTHROPIC_API_KEY')
                    ? 'anthropic'
                    : (env('GEMINI_API_KEY') ? 'gemini' : null))),
        'system_prompt' => env('AI_TEST_SYSTEM_PROMPT', 'You are a concise assistant helping verify an AI provider integration.'),
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => rtrim(env('OPENAI_BASE_URL', 'https://api.openai.com/v1'), '/'),
            'model' => env('OPENAI_MODEL', env('AI_MODEL', 'gpt-4.1-mini')),
        ],
        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'base_url' => rtrim(env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'), '/'),
            'model' => env('ANTHROPIC_MODEL', env('AI_MODEL', 'claude-3-5-haiku-latest')),
        ],
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => rtrim(env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'), '/'),
            'model' => env('GEMINI_MODEL', env('AI_MODEL', 'gemini-2.0-flash')),
        ],
    ],

];
