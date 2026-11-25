<?php

namespace Tests\Feature;

use App\Actions\DispatchDreamAnalysis;
use App\Jobs\AnalyzeDream;
use App\Models\DreamAnalysis;
use App\Models\DreamEntry;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use App\Services\Profile\ProfileConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AutoDreamAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);
        UserProfile::query()->create(['user_id' => $user->id]);
        UserPreference::query()->create(['user_id' => $user->id, 'locale' => 'ar']);
        app(ProfileConsentService::class)->ensureDefaults($user);

        return $user;
    }

    public function test_storing_dream_with_text_dispatches_auto_analysis(): void
    {
        Bus::fake();
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('dreams.store'), [
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلمت أنني أسقط في نهر.',
            'mood_upon_waking' => 'قلق',
        ])->assertRedirect();

        Bus::assertDispatched(AnalyzeDream::class);
        $this->assertDatabaseHas('dream_entries', [
            'user_id' => $user->id,
            'processing_status' => 'analyzing',
        ]);
    }

    public function test_does_not_redispatch_when_completed_analysis_exists(): void
    {
        Bus::fake();
        $user = $this->makeUser();
        $dream = DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم سابق',
            'corrected_text' => 'حلم سابق',
            'status' => 'analyzed',
            'processing_status' => 'completed',
        ]);

        DreamAnalysis::query()->create([
            'dream_entry_id' => $dream->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'summary' => 'done',
            'payload' => ['dream_summary' => 'done'],
        ]);

        $dispatched = app(DispatchDreamAnalysis::class)->handle($dream);

        $this->assertFalse($dispatched);
        Bus::assertNotDispatched(AnalyzeDream::class);
    }

    public function test_dream_show_hides_manual_analyze_cta(): void
    {
        $user = $this->makeUser();
        $dream = DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم',
            'status' => 'recorded',
            'processing_status' => 'idle',
        ]);

        $html = $this->actingAs($user)->get(route('dreams.show', $dream))->assertOk()->getContent();

        $this->assertStringNotContainsString('تحليل بالذكاء الاصطناعي', $html);
        $this->assertStringNotContainsString('dreams.analyze', $html);
    }
}
