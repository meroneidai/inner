<?php

namespace Tests\Feature;

use App\Models\DreamEntry;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class DreamJournalTest extends TestCase
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

    public function test_user_can_create_dream(): void
    {
        Bus::fake();
        $user = $this->makeUser();

        $response = $this->actingAs($user)->post(route('dreams.store'), [
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلمت أنني أسقط في نهر هادئ.',
            'mood_upon_waking' => 'قلق',
            'sleep_quality' => 6,
            'emotional_intensity' => 7,
        ]);

        $dream = DreamEntry::query()->first();
        $response->assertRedirect(route('dreams.show', $dream));
        $this->assertDatabaseHas('dream_entries', [
            'user_id' => $user->id,
            'original_text' => 'حلمت أنني أسقط في نهر هادئ.',
        ]);
    }

    public function test_user_cannot_view_another_users_dream(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();

        $dream = DreamEntry::query()->create([
            'user_id' => $owner->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم خاص',
            'status' => 'recorded',
        ]);

        $this->actingAs($other)->get(route('dreams.show', $dream))->assertForbidden();
    }

    public function test_user_can_delete_own_dream(): void
    {
        $user = $this->makeUser();
        $dream = DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم للحذف',
            'status' => 'recorded',
        ]);

        $this->actingAs($user)->delete(route('dreams.destroy', $dream))->assertRedirect(route('dreams.index'));
        $this->assertSoftDeleted('dream_entries', ['id' => $dream->id]);
    }
}
