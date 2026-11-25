<?php

namespace App\Services\AI;

use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\OpenRouterProvider;
use InvalidArgumentException;

class AIManager
{
    public function driver(?string $name = null): AIProviderInterface
    {
        $name = $name ?: AiSettings::provider();

        return match ($name) {
            'openrouter' => app(OpenRouterProvider::class),
            'openai' => app(OpenAIProvider::class),
            default => throw new InvalidArgumentException("Unsupported AI provider [{$name}]."),
        };
    }
}
