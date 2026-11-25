<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{content: string, model: string, usage: array{prompt_tokens: int, completion_tokens: int, total_tokens: int}}
     */
    public function chat(array $messages, array $options = []): array;

    public function name(): string;
}
