<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use App\Services\AI\AiSettings;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterProvider implements AIProviderInterface
{
    public function name(): string
    {
        return 'openrouter';
    }

    public function chat(array $messages, array $options = []): array
    {
        $apiKey = AiSettings::get('openrouter_api_key') ?: config('ai.openrouter.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENROUTER_API_KEY is not configured.');
        }

        $model = $options['model']
            ?? AiSettings::get('analysis_model')
            ?? config('ai.openrouter.analysis_model');

        $baseUrl = rtrim((string) (AiSettings::get('openrouter_base_url') ?: config('ai.openrouter.base_url')), '/');

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature']
                ?? (float) (AiSettings::get('temperature') ?? config('ai.openrouter.temperature')),
            'max_tokens' => $options['max_tokens']
                ?? (int) (AiSettings::get('max_tokens') ?? config('ai.openrouter.max_tokens')),
        ];

        if (($options['response_format'] ?? ['type' => 'json_object']) !== false) {
            $payload['response_format'] = $options['response_format'] ?? ['type' => 'json_object'];
        }

        $headers = [
            'HTTP-Referer' => AiSettings::get('openrouter_site_url') ?: config('app.url'),
            'X-Title' => AiSettings::get('openrouter_app_name') ?: config('app.name', 'InnerPath'),
        ];

        $response = Http::timeout((int) config('ai.timeout', 120))
            ->withToken($apiKey)
            ->withHeaders($headers)
            ->acceptJson()
            ->asJson()
            ->post("{$baseUrl}/chat/completions", $payload);

        if ($response->failed()) {
            throw new RuntimeException('OpenRouter chat request failed: '.$response->body());
        }

        $data = $response->json();
        $content = data_get($data, 'choices.0.message.content');

        if (! is_string($content) || $content === '') {
            throw new RuntimeException('OpenRouter returned an empty response.');
        }

        return [
            'content' => $content,
            'model' => (string) data_get($data, 'model', $model),
            'usage' => [
                'prompt_tokens' => (int) data_get($data, 'usage.prompt_tokens', 0),
                'completion_tokens' => (int) data_get($data, 'usage.completion_tokens', 0),
                'total_tokens' => (int) data_get($data, 'usage.total_tokens', 0),
            ],
        ];
    }
}
