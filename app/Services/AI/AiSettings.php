<?php

namespace App\Services\AI;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class AiSettings
{
    public static function isConfigured(): bool
    {
        $provider = self::provider();

        if ($provider === 'openrouter') {
            return filled(self::get('openrouter_api_key') ?: config('ai.openrouter.api_key'));
        }

        return filled(config('ai.openai.api_key'));
    }

    public const CACHE_KEY = 'innerpath.ai_settings';

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            $db = SystemSetting::query()
                ->where('group', 'ai')
                ->pluck('value', 'key')
                ->all();

            return array_merge(self::defaultsFromEnv(), $db);
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        return $all[$key] ?? $default;
    }

    public static function put(string $key, ?string $value): void
    {
        SystemSetting::query()->updateOrCreate(
            ['group' => 'ai', 'key' => $key],
            ['value' => $value]
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }
            self::put($key, is_bool($value) ? ($value ? '1' : '0') : (string) $value);
        }
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, string|null>
     */
    public static function defaultsFromEnv(): array
    {
        return [
            'provider' => config('ai.provider', 'openrouter'),
            'analysis_model' => config('ai.openrouter.analysis_model', 'openai/gpt-4o'),
            'chat_model' => config('ai.openrouter.model', 'openai/gpt-4o-mini'),
            'temperature' => (string) config('ai.openrouter.temperature', 0.4),
            'max_tokens' => (string) config('ai.openrouter.max_tokens', 4096),
            'openrouter_api_key' => config('ai.openrouter.api_key'),
            'openrouter_base_url' => config('ai.openrouter.base_url', 'https://openrouter.ai/api/v1'),
            'openrouter_site_url' => config('app.url'),
            'openrouter_app_name' => config('app.name', 'InnerPath'),
            'auto_analyze' => (string) (config('ai.auto_analyze') ? '1' : '0'),
        ];
    }

    public static function provider(): string
    {
        return (string) self::get('provider', 'openrouter');
    }

    public static function analysisModel(): string
    {
        return (string) self::get('analysis_model', 'openai/gpt-4o');
    }

    public static function autoAnalyzeEnabled(): bool
    {
        return filter_var(self::get('auto_analyze', '1'), FILTER_VALIDATE_BOOLEAN);
    }
}
