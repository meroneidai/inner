<?php

return [

    /*
    | Default analysis provider. Prefer openrouter (ChatGPT via OpenRouter).
    */
    'provider' => env('AI_PROVIDER', 'openrouter'),

    'timeout' => (int) env('AI_TIMEOUT', 120),

    'max_retries' => (int) env('AI_MAX_RETRIES', 3),

    /*
    | Automatically queue dream analysis after text is available (first time only).
    */
    'auto_analyze' => filter_var(env('AI_AUTO_ANALYZE', true), FILTER_VALIDATE_BOOLEAN),

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model' => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
        'analysis_model' => env('OPENROUTER_ANALYSIS_MODEL', env('OPENROUTER_MODEL', 'openai/gpt-4o')),
        'max_tokens' => (int) env('OPENROUTER_MAX_TOKENS', 4096),
        'temperature' => (float) env('OPENROUTER_TEMPERATURE', 0.4),
        'site_url' => env('OPENROUTER_SITE_URL', env('APP_URL')),
        'app_name' => env('OPENROUTER_APP_NAME', env('APP_NAME', 'InnerPath')),
    ],

    // Kept for Whisper / fallback chat if needed
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'analysis_model' => env('OPENAI_ANALYSIS_MODEL', 'gpt-4o'),
        'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 4096),
        'temperature' => (float) env('OPENAI_TEMPERATURE', 0.4),
    ],

    'transcription' => [
        'provider' => env('TRANSCRIPTION_PROVIDER', 'openai'),
        'model' => env('OPENAI_WHISPER_MODEL', 'whisper-1'),
        'language' => env('TRANSCRIPTION_LANGUAGE', 'ar'),
        'timeout' => (int) env('TRANSCRIPTION_TIMEOUT', 180),
    ],

    'pattern_weights' => [
        'frequency' => (float) env('PATTERN_WEIGHT_FREQUENCY', 0.30),
        'recent' => (float) env('PATTERN_WEIGHT_RECENT', 0.25),
        'context' => (float) env('PATTERN_WEIGHT_CONTEXT', 0.20),
        'emotion' => (float) env('PATTERN_WEIGHT_EMOTION', 0.15),
        'temporal' => (float) env('PATTERN_WEIGHT_TEMPORAL', 0.10),
    ],

];
