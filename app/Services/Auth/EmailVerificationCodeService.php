<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmailVerificationCodeService
{
    public const CODE_LENGTH = 6;

    public const EXPIRES_MINUTES = 30;

    /**
     * Generate a fresh numeric code, persist its hash, and email it.
     */
    public function issue(User $user): void
    {
        $code = $this->generateCode();

        $user->forceFill([
            'email_verification_code' => Hash::make($code),
            'email_verification_code_expires_at' => now()->addMinutes(self::EXPIRES_MINUTES),
        ])->save();

        $user->notify(new EmailVerificationCodeNotification($code));
    }

    /**
     * @throws ValidationException
     */
    public function verify(User $user, string $code): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (
            blank($user->email_verification_code)
            || blank($user->email_verification_code_expires_at)
            || $user->email_verification_code_expires_at->isPast()
        ) {
            throw ValidationException::withMessages([
                'code' => __('انتهت صلاحية رمز التأكيد. اطلب رمزاً جديداً.'),
            ]);
        }

        if (! Hash::check($code, $user->email_verification_code)) {
            throw ValidationException::withMessages([
                'code' => __('رمز التأكيد غير صحيح.'),
            ]);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ])->save();
    }

    private function generateCode(): string
    {
        $max = (10 ** self::CODE_LENGTH) - 1;

        return str_pad((string) random_int(0, $max), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }
}
