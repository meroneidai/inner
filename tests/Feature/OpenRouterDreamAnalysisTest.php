<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeDream;
use App\Models\DreamEntry;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use App\Services\Profile\ProfileConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenRouterDreamAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);
        UserProfile::query()->create(['user_id' => $user->id, 'completion_percent' => 10]);
        UserPreference::query()->create(['user_id' => $user->id, 'locale' => 'ar']);
        app(ProfileConsentService::class)->ensureDefaults($user);

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'dream_summary' => 'حلم عن نهر هادئ',
            'narrative_sequence' => [
                ['type' => 'scene', 'description' => 'السقوط في النهر'],
            ],
            'main_entities' => ['نهر'],
            'main_symbols' => [
                [
                    'symbol' => 'نهر',
                    'context' => 'ماء جارٍ',
                    'interpretation_confidence' => 70,
                    'evidence_level' => 'moderate',
                ],
            ],
            'emotional_analysis' => [
                'identified_emotions' => [
                    ['emotion' => 'قلق', 'intensity' => 6, 'context' => 'عند الاستيقاظ'],
                ],
            ],
            'psychological_reflection' => [
                'themes' => ['تحول'],
            ],
            'historical_and_cultural_analysis' => [
                [
                    'tradition' => 'عام',
                    'source' => 'رمزي',
                    'interpretation' => 'الماء رمز للتدفق',
                ],
            ],
            'personal_context_connections' => [],
            'alternative_interpretations' => ['تجديد'],
            'missing_information' => [],
            'reflection_questions' => ['ماذا يعني الماء لك؟'],
            'personal_growth_actions' => ['تأمل قصير'],
            'overall_interpretation' => 'إشارة إلى انتقال عاطفي',
            'limitations' => ['تفسير احتمالي'],
            'analysis_confidence' => 72,
        ];
    }

    public function test_analyze_dream_via_openrouter_stores_completed_analysis(): void
    {
        config([
            'ai.provider' => 'openrouter',
            'ai.openrouter.api_key' => 'test-openrouter-key',
            'ai.openrouter.analysis_model' => 'openai/gpt-4o',
            'ai.openrouter.base_url' => 'https://openrouter.ai/api/v1',
            'ai.auto_analyze' => true,
        ]);

        Http::fake([
            'openrouter.ai/*' => Http::response([
                'id' => 'gen-test',
                'model' => 'openai/gpt-4o',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => json_encode($this->validPayload(), JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 120,
                    'completion_tokens' => 80,
                    'total_tokens' => 200,
                ],
            ], 200),
        ]);

        $user = $this->makeUser();
        $dream = DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلمت أنني أسقط في نهر هادئ.',
            'status' => 'recorded',
            'processing_status' => 'idle',
        ]);

        app()->call([new AnalyzeDream($dream), 'handle']);

        $this->assertDatabaseHas('dream_analyses', [
            'dream_entry_id' => $dream->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'provider' => 'openrouter',
            'model' => 'openai/gpt-4o',
        ]);

        $this->assertDatabaseHas('dream_entries', [
            'id' => $dream->id,
            'status' => 'analyzed',
            'processing_status' => 'completed',
        ]);

        $this->assertDatabaseHas('ai_usage_logs', [
            'user_id' => $user->id,
            'provider' => 'openrouter',
            'operation' => 'dream_analysis',
            'status' => 'success',
            'total_tokens' => 200,
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'openrouter.ai')
                && $request['model'] === 'openai/gpt-4o'
                && is_array($request['messages']);
        });
    }

    public function test_missing_openrouter_key_marks_analysis_failed_without_throwing(): void
    {
        config([
            'ai.provider' => 'openrouter',
            'ai.openrouter.api_key' => '',
        ]);

        $user = $this->makeUser();
        $dream = DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم بدون مفتاح',
            'status' => 'recorded',
            'processing_status' => 'idle',
        ]);

        app()->call([new AnalyzeDream($dream), 'handle']);

        $this->assertDatabaseHas('dream_analyses', [
            'dream_entry_id' => $dream->id,
            'status' => 'failed',
        ]);
        $this->assertDatabaseHas('dream_entries', [
            'id' => $dream->id,
            'processing_status' => 'failed',
        ]);
    }
}
