<?php

namespace Tests\Feature;

use App\Jobs\GenerateWeeklyReport;
use App\Jobs\SendDailyCareNotifications;
use App\Jobs\SendDailyDreamReminders;
use App\Models\DreamAnalysis;
use App\Models\DreamEntry;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use App\Models\WeeklyReport;
use App\Notifications\DailyCareNotification;
use App\Notifications\DailyDreamReminderNotification;
use App\Notifications\DreamAnalysisReadyNotification;
use App\Notifications\WeeklyReportReadyNotification;
use App\Services\Care\DailyCareService;
use App\Services\Notifications\NotificationGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $pref = []): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
            'is_active' => true,
        ]);
        UserProfile::query()->create(['user_id' => $user->id, 'preferred_name' => 'مريم']);
        UserPreference::query()->create(array_merge([
            'user_id' => $user->id,
            'locale' => 'ar',
            'email_notifications' => true,
            'in_app_notifications' => true,
            'daily_care_enabled' => true,
            'daily_reminder_enabled' => true,
            'analysis_ready_enabled' => true,
            'growth_reminder_enabled' => true,
            'weekly_report_enabled' => true,
            'monthly_report_enabled' => true,
            'daily_care_hour' => (int) now()->format('G'),
            'daily_reminder_hour' => (int) now()->format('G'),
        ], $pref));

        return $user->fresh(['preferences', 'profile']);
    }

    public function test_daily_care_job_notifies_eligible_users(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        (new SendDailyCareNotifications)->handle(
            app(DailyCareService::class),
            app(NotificationGateway::class)
        );

        Notification::assertSentTo($user, DailyCareNotification::class);
    }

    public function test_daily_reminder_skips_users_with_today_dream(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم اليوم',
            'status' => 'recorded',
        ]);

        (new SendDailyDreamReminders)->handle(app(NotificationGateway::class));

        Notification::assertNotSentTo($user, DailyDreamReminderNotification::class);
    }

    public function test_daily_reminder_notifies_when_no_dream_today(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        (new SendDailyDreamReminders)->handle(app(NotificationGateway::class));

        Notification::assertSentTo($user, DailyDreamReminderNotification::class);
    }

    public function test_analysis_ready_notification_respects_preference(): void
    {
        Notification::fake();
        $user = $this->makeUser(['analysis_ready_enabled' => true]);
        $dream = DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم',
            'status' => 'analyzed',
        ]);
        $analysis = DreamAnalysis::query()->create([
            'dream_entry_id' => $dream->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'summary' => 'ملخص',
            'payload' => ['dream_summary' => 'ملخص'],
        ]);

        app(NotificationGateway::class)->send($user, new DreamAnalysisReadyNotification($analysis));

        Notification::assertSentTo($user, DreamAnalysisReadyNotification::class);
    }

    public function test_notifications_index_and_mark_read(): void
    {
        $user = $this->makeUser();
        $user->notify(new DailyDreamReminderNotification);

        $this->actingAs($user)->get(route('notifications.index'))->assertOk();

        $id = $user->notifications()->first()->id;
        $this->actingAs($user)->post(route('notifications.read', $id))->assertRedirect();
        $this->assertNotNull($user->fresh()->notifications()->first()->read_at);
    }

    public function test_settings_can_update_notification_preferences(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->patch(route('settings.update'), [
            'locale' => 'ar',
            'email_notifications' => '1',
            'in_app_notifications' => '1',
            'daily_care_enabled' => '1',
            'weekly_report_enabled' => '1',
            'daily_care_hour' => 7,
            'daily_reminder_hour' => 21,
        ])->assertRedirect();

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'daily_care_hour' => 7,
            'daily_reminder_hour' => 21,
            'email_notifications' => 1,
        ]);
    }

    public function test_weekly_report_job_sends_notification(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        DreamEntry::query()->create([
            'user_id' => $user->id,
            'dream_date' => now()->toDateString(),
            'original_text' => 'حلم أسبوعي',
            'status' => 'analyzed',
            'processing_status' => 'completed',
        ]);

        // Bypass AI: generate with insufficient or completed via service path.
        (new GenerateWeeklyReport($user))->handle(
            app(\App\Services\AI\WeeklyAnalysisService::class),
            app(NotificationGateway::class)
        );

        Notification::assertSentTo($user, WeeklyReportReadyNotification::class);
        $this->assertDatabaseHas('weekly_reports', ['user_id' => $user->id]);
    }
}
