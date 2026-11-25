<?php

namespace Tests\Feature;

use App\Models\DreamAnalysis;
use App\Models\DreamEntry;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use App\Services\Growth\PersonalGrowthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisActionFeedbackTest extends TestCase
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

        return $user;
    }

    public function test_analysis_page_shows_actions_and_accepts_icon_feedback(): void
    {
        $user = $this->makeUser();
        $dream = DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم اختبار',
            'status' => 'analyzed',
            'processing_status' => 'completed',
        ]);

        $analysis = DreamAnalysis::query()->create([
            'dream_entry_id' => $dream->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'summary' => 'ملخص',
            'analysis_confidence' => 70,
            'payload' => [
                'dream_summary' => 'ملخص',
                'personal_growth_actions' => ['تنفس بعمق 3 مرات اليوم', 'راجع رمزاً واحداً هذا الأسبوع'],
                'step_by_step_growth' => [
                    ['step' => 1, 'title' => 'هدوء صباحي', 'why' => 'يهدئ الجهاز العصبي', 'focus' => 'calm'],
                ],
                'main_symbols' => [],
                'psychological_reflection' => ['possible_themes' => ['طمأنينة']],
                'reflection_questions' => ['ماذا احتجت؟'],
                'limitations' => ['تفسير احتمالي'],
                'care_tags' => [['emoji' => '🌿', 'label' => 'هدوء']],
            ],
        ]);

        app(PersonalGrowthService::class)->suggestFromAnalysis($user, $analysis);

        $html = $this->actingAs($user)->get(route('analyses.show', $analysis))->assertOk()->getContent();
        $this->assertStringContainsString('ماذا أفعل حيال ذلك؟', $html);
        $this->assertStringContainsString('هدوء صباحي', $html);

        $this->actingAs($user)->post(route('analyses.feedback', $analysis), [
            'relevance' => 'relevant',
            'accuracy_rating' => 4,
            'will_try_action' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('analysis_feedback', [
            'dream_analysis_id' => $analysis->id,
            'user_id' => $user->id,
            'relevance' => 'relevant',
            'accuracy_rating' => 4,
            'will_try_action' => 1,
        ]);

        $this->assertDatabaseHas('development_actions', [
            'dream_analysis_id' => $analysis->id,
            'cadence' => 'daily',
        ]);
    }
}
