<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
        $response->assertSee(__('رمز التأكيد'), false);
    }

    public function test_email_can_be_verified_with_numeric_code(): void
    {
        Notification::fake();
        Event::fake([Verified::class]);

        $user = User::factory()->unverified()->create();
        $plainCode = '482913';

        $user->forceFill([
            'email_verification_code' => Hash::make($plainCode),
            'email_verification_code_expires_at' => now()->addMinutes(30),
        ])->save();

        $response = $this->actingAs($user)->post(route('verification.verify'), [
            'code' => $plainCode,
        ]);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_email_is_not_verified_with_invalid_code(): void
    {
        $user = User::factory()->unverified()->create();

        $user->forceFill([
            'email_verification_code' => Hash::make('123456'),
            'email_verification_code_expires_at' => now()->addMinutes(30),
        ])->save();

        $response = $this->actingAs($user)->from(route('verification.notice'))->post(route('verification.verify'), [
            'code' => '000000',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_code_can_be_resent(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-code-sent');
        Notification::assertSentTo($user, EmailVerificationCodeNotification::class);
        $this->assertNotNull($user->fresh()->email_verification_code);
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $user->forceFill([
            'email_verification_code' => Hash::make('654321'),
            'email_verification_code_expires_at' => now()->subMinute(),
        ])->save();

        $response = $this->actingAs($user)->from(route('verification.notice'))->post(route('verification.verify'), [
            'code' => '654321',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_registration_issues_verification_code_and_stays_on_notice(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'otp@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'otp@example.com')->first();

        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('verification.notice', absolute: false));
        Notification::assertSentTo($user, EmailVerificationCodeNotification::class);
        $this->assertNotNull($user->fresh()->email_verification_code);
    }
}
