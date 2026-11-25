<?php

namespace Tests\Unit;

use App\Services\Analytics\PatternDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\DreamEntry;
use App\Models\DreamSymbol;
use App\Models\DreamSymbolOccurrence;

class PatternDetectionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_insufficient_data_when_fewer_than_two_dreams(): void
    {
        $user = User::factory()->create();
        DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم واحد',
            'status' => 'recorded',
        ]);

        $result = app(PatternDetectionService::class)->detect($user);

        $this->assertSame('insufficient_data', $result['status']);
    }

    public function test_pattern_score_is_calculated(): void
    {
        $user = User::factory()->create();
        $symbol = DreamSymbol::query()->create([
            'slug' => 'river',
            'name_ar' => 'نهر',
            'name_en' => 'river',
            'normalized_key' => 'نهر',
        ]);

        foreach ([0, 1, 3] as $daysAgo) {
            $dream = DreamEntry::query()->create([
                'user_id' => $user->id,
                'dream_date' => now()->subDays($daysAgo)->toDateString(),
                'original_text' => 'نهر',
                'status' => 'analyzed',
            ]);
            DreamSymbolOccurrence::query()->create([
                'dream_entry_id' => $dream->id,
                'dream_symbol_id' => $symbol->id,
                'context' => 'falling into river',
            ]);
        }

        $result = app(PatternDetectionService::class)->detect($user);

        $this->assertSame('ok', $result['status']);
        $this->assertNotEmpty($result['patterns']);
        $this->assertArrayHasKey('pattern_score', $result['patterns'][0]);
    }
}
