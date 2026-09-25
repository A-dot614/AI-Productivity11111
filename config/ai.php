<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | The provider used when no provider is stored in the database settings.
    | Supported drivers: "openai", "gemini". The value stored in the
    | database (settings table) takes precedence over this default.
    |
    */
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),

    'providers' => [

        'openai' => [
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 1500),
            'timeout' => (int) env('OPENAI_TIMEOUT', 60),
        ],

        'gemini' => [
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
            'max_tokens' => (int) env('GEMINI_MAX_TOKENS', 1500),
            'timeout' => (int) env('GEMINI_TIMEOUT', 60),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Identical AI requests can be cached to reduce cost during development.
    |
    */
    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', false),
        'ttl' => (int) env('AI_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | When enabled, every AI interaction is persisted to the ai_histories table.
    |
    */
    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
    ],

];
