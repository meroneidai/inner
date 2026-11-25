<?php

namespace Tests\Unit;

use App\Services\AI\ResponseValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ResponseValidatorTest extends TestCase
{
    public function test_valid_payload_passes(): void
    {
        $payload = [
            'dream_summary' => 'summary',
            'narrative_sequence' => [],
            'main_entities' => [],
            'main_symbols' => [],
            'emotional_analysis' => [],
            'psychological_reflection' => [],
            'historical_and_cultural_analysis' => [],
            'personal_context_connections' => [],
            'alternative_interpretations' => [],
            'missing_information' => [],
            'reflection_questions' => [],
            'personal_growth_actions' => [],
            'overall_interpretation' => 'overall',
            'limitations' => [],
            'analysis_confidence' => 55,
        ];

        $result = (new ResponseValidator)->validateDreamAnalysis(json_encode($payload));

        $this->assertSame('summary', $result['dream_summary']);
    }

    public function test_missing_key_fails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ResponseValidator)->validateDreamAnalysis(json_encode(['dream_summary' => 'x']));
    }
}
