<?php

namespace App\Services\Transcription;

interface TranscriptionProviderInterface
{
    /**
     * @return array{text: string, language: ?string, confidence: ?float, provider: string}
     */
    public function transcribe(string $absolutePath, ?string $language = null): array;

    public function name(): string;
}
