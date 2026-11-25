<?php

namespace App\Services\Transcription;

use App\Services\Transcription\Providers\OpenAIWhisperProvider;
use InvalidArgumentException;

class TranscriptionManager
{
    public function driver(?string $name = null): TranscriptionProviderInterface
    {
        $name = $name ?: config('ai.transcription.provider', 'openai');

        return match ($name) {
            'openai' => app(OpenAIWhisperProvider::class),
            default => throw new InvalidArgumentException("Unsupported transcription provider [{$name}]."),
        };
    }
}
