<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIProvider implements AIProviderInterface
{
    public function name(): string
    {
        return 'openai';
    }

    public function chat(array $messages, array $options = []): array
    {
        $apiKey = config('ai.openai.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }

        $model = $options['model'] ?? config('ai.openai.analysis_model');
        $baseUrl = rtrim((string) config('ai.openai.base_url'), '/');

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? config('ai.openai.temperature'),
            'max_tokens' => $options['max_tokens'] ?? config('ai.openai.max_tokens'),
            'response_format' => $options['response_format'] ?? ['type' => 'json_object'],
        ];

        $request = Http::timeout((int) config('ai.timeout', 120))
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson();

        if ($org = config('ai.openai.organization')) {
            $request = $request->withHeaders(['OpenAI-Organization' => $org]);
        }

        $response = $request->post("{$baseUrl}/chat/completions", $payload);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI chat request failed: '.$response->body());
        }

        $data = $response->json();
        $content = data_get($data, 'choices.0.message.content');

        if (! is_string($content) || $content === '') {
            throw new RuntimeException('OpenAI returned an empty response.');
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
