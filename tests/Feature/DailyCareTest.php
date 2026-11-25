<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use App\Services\Care\DailyCareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyCareTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_care_messages_are_created_and_shown_on_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);
        UserProfile::query()->create([
            'user_id' => $user->id,
            'preferred_name' => 'مريم',
            'completion_percent' => 10,
            'emotional_state' => 'قلق',
        ]);
        UserPreference::query()->create(['user_id' => $user->id, 'locale' => 'ar']);

        $messages = app(DailyCareService::class)->forToday($user);

        $this->assertTrue($messages->count() >= 2);
        $this->assertDatabaseHas('daily_care_messages', [
            'user_id' => $user->id,
        ]);

        $html = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();
        $this->assertStringContainsString('مريم', $html);
    }
}
