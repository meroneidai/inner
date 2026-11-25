<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use App\Services\Auth\EmailVerificationCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EmailVerificationCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_stores_hashed_code_and_notifies_user(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        app(EmailVerificationCodeService::class)->issue($user);

        $user->refresh();

        $this->assertNotNull($user->email_verification_code);
        $this->assertTrue($user->email_verification_code_expires_at->isFuture());
        Notification::assertSentTo($user, EmailVerificationCodeNotification::class);
    }

    public function test_verify_accepts_matching_code(): void
    {
        $user = User::factory()->unverified()->create();
        $code = '111222';

        $user->forceFill([
            'email_verification_code' => Hash::make($code),
            'email_verification_code_expires_at' => now()->addMinutes(10),
        ])->save();

        app(EmailVerificationCodeService::class)->verify($user, $code);

        $user->refresh();

        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertNull($user->email_verification_code);
    }

    public function test_verify_rejects_wrong_code(): void
    {
        $user = User::factory()->unverified()->create();

        $user->forceFill([
            'email_verification_code' => Hash::make('111222'),
            'email_verification_code_expires_at' => now()->addMinutes(10),
        ])->save();

        $this->expectException(ValidationException::class);

        app(EmailVerificationCodeService::class)->verify($user, '999999');
    }
}
