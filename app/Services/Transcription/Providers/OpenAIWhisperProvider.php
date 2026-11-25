<?php

namespace App\Services\Transcription\Providers;

use App\Services\Transcription\TranscriptionProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIWhisperProvider implements TranscriptionProviderInterface
{
    public function name(): string
    {
        return 'openai';
    }

    public function transcribe(string $absolutePath, ?string $language = null): array
    {
        $apiKey = config('ai.openai.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }

        if (! is_file($absolutePath)) {
            throw new RuntimeException('Audio file not found for transcription.');
        }

        $baseUrl = rtrim((string) config('ai.openai.base_url'), '/');
        $language = $language ?: config('ai.transcription.language', 'ar');

        $response = Http::timeout((int) config('ai.transcription.timeout', 180))
            ->withToken($apiKey)
            ->attach('file', file_get_contents($absolutePath), basename($absolutePath))
            ->post("{$baseUrl}/audio/transcriptions", array_filter([
                'model' => config('ai.transcription.model', 'whisper-1'),
                'language' => $language,
                'response_format' => 'verbose_json',
            ]));

        if ($response->failed()) {
            throw new RuntimeException('Whisper transcription failed: '.$response->body());
        }

        $data = $response->json();

        return [
            'text' => (string) ($data['text'] ?? ''),
            'language' => $data['language'] ?? $language,
            'confidence' => isset($data['confidence']) ? (float) $data['confidence'] : null,
            'provider' => $this->name(),
        ];
    }
}
