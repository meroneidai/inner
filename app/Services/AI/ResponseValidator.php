<?php

namespace App\Services\AI;

use InvalidArgumentException;

class ResponseValidator
{
    /**
     * @return array<string, mixed>
     */
    public function validateDreamAnalysis(string $json): array
    {
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new InvalidArgumentException('AI response is not valid JSON.');
        }

        $required = [
            'dream_summary',
            'narrative_sequence',
            'main_entities',
            'main_symbols',
            'emotional_analysis',
            'psychological_reflection',
            'historical_and_cultural_analysis',
            'personal_context_connections',
            'alternative_interpretations',
            'missing_information',
            'reflection_questions',
            'personal_growth_actions',
            'overall_interpretation',
            'limitations',
            'analysis_confidence',
        ];

        foreach ($required as $key) {
            if (! array_key_exists($key, $decoded)) {
                throw new InvalidArgumentException("AI response missing required key [{$key}].");
            }
        }

        if (! is_array($decoded['main_symbols'])) {
            throw new InvalidArgumentException('main_symbols must be an array.');
        }

        if (! is_array($decoded['emotional_analysis'])) {
            throw new InvalidArgumentException('emotional_analysis must be an object.');
        }

        if (! is_array($decoded['psychological_reflection'])) {
            throw new InvalidArgumentException('psychological_reflection must be an object.');
        }

        $confidence = $decoded['analysis_confidence'];
        if (! is_numeric($confidence) || $confidence < 0 || $confidence > 100) {
            throw new InvalidArgumentException('analysis_confidence must be a number between 0 and 100.');
        }

        // Optional companion fields — normalize safely without failing older responses.
        $decoded['care_tags'] = is_array($decoded['care_tags'] ?? null) ? $decoded['care_tags'] : [];
        $decoded['support_focus'] = is_array($decoded['support_focus'] ?? null) ? $decoded['support_focus'] : [];
        $decoded['step_by_step_growth'] = is_array($decoded['step_by_step_growth'] ?? null) ? $decoded['step_by_step_growth'] : [];
        $decoded['daily_encouragement'] = is_string($decoded['daily_encouragement'] ?? null)
            ? $decoded['daily_encouragement']
            : '';

        foreach ($decoded['main_symbols'] as &$symbol) {
            if (! is_array($symbol)) {
                continue;
            }
            $symbol['personal_associations'] = is_array($symbol['personal_associations'] ?? null)
                ? $symbol['personal_associations']
                : [];
        }
        unset($symbol);

        return $decoded;
    }
}
