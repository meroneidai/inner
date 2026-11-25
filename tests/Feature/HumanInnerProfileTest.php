<?php

namespace Tests\Feature;

use App\Models\ProfileAnswer;
use App\Models\ProfileObservation;
use App\Models\ProfileQuestion;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use App\Services\Profile\ProfileAnswerService;
use App\Services\Profile\ProfileConsentService;
use App\Services\Profile\ProfileContextBuilder;
use App\Services\Profile\ProfileObservationService;
use Database\Seeders\ProfileQuestionBankSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HumanInnerProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        UserProfile::query()->create(['user_id' => $user->id, 'profile_status' => 'not_started']);
        UserPreference::query()->create(['user_id' => $user->id, 'locale' => 'ar']);
        app(ProfileConsentService::class)->ensureDefaults($user);

        return $user;
    }

    public function test_user_can_skip_onboarding(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('onboarding.skip'))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($user->fresh()->onboarding_completed_at);
        $this->assertSame('skipped', $user->profile->fresh()->profile_status);
    }

    public function test_user_can_save_stage1_answers_and_resume(): void
    {
        $this->seed(ProfileQuestionBankSeeder::class);
        $user = $this->makeUser();
        $question = ProfileQuestion::query()->where('question_key', 'A01')->firstOrFail();

        $this->actingAs($user)->post(route('onboarding.stage1.save'), [
            'action' => 'exit',
            'answers' => [
                $question->id => 'نور',
            ],
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('profile_answers', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'answer_value' => 'نور',
        ]);
    }

    public function test_user_cannot_access_another_users_observation_actions(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $obs = ProfileObservation::query()->create([
            'user_id' => $owner->id,
            'observation' => 'hypothesis',
            'source_type' => 'ai_hypothesis',
            'status' => 'pending',
            'user_confirmation_status' => 'pending',
        ]);

        $this->actingAs($other)
            ->post(route('inner-world.observations.confirm', $obs))
            ->assertForbidden();
    }

    public function test_hypothesis_is_not_auto_confirmed(): void
    {
        $user = $this->makeUser();
        $obs = app(ProfileObservationService::class)->createHypothesis($user, 'Maybe theme X');

        $this->assertSame('pending', $obs->status);
        $this->assertSame('ai_hypothesis', $obs->source_type);
        $this->assertFalse($obs->isConfirmed());
    }

    public function test_revoked_consent_excludes_profile_from_dream_context(): void
    {
        $this->seed(ProfileQuestionBankSeeder::class);
        $user = $this->makeUser();
        $question = ProfileQuestion::query()->where('question_key', 'B01')->firstOrFail();
        app(ProfileAnswerService::class)->save($user, $question, 'قلق');

        app(ProfileConsentService::class)->revoke($user, ProfileConsentService::ANALYTICAL_USE);

        $dream = $user->dreams()->create([
            'dream_date' => now()->toDateString(),
            'original_text' => 'نهر',
            'status' => 'recorded',
        ]);

        $context = app(ProfileContextBuilder::class)->forDream($user, $dream);

        $this->assertSame([], $context['items']);
        $this->assertContains('analytical_consent_missing_or_revoked', $context['excluded_reasons']);
    }

    public function test_answer_history_is_preserved_on_update(): void
    {
        $this->seed(ProfileQuestionBankSeeder::class);
        $user = $this->makeUser();
        $question = ProfileQuestion::query()->where('question_key', 'A01')->firstOrFail();
        $service = app(ProfileAnswerService::class);

        $service->save($user, $question, 'أول');
        $service->save($user, $question, 'ثاني');

        $this->assertDatabaseHas('profile_answer_histories', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'previous_value' => 'أول',
            'new_value' => 'ثاني',
        ]);

        $this->assertSame(1, ProfileAnswer::query()->where('user_id', $user->id)->where('question_id', $question->id)->count());
    }
}
