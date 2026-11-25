<?php

namespace App\Services\Profile;

use App\Models\ProfileConsent;
use App\Models\User;

class ProfileConsentService
{
    public const ANALYTICAL_USE = 'analytical_use';

    public const SENSITIVE_DISCLOSURE = 'sensitive_disclosure';

    public const AI_TRAINING = 'ai_training';

    public const SLEEP_SUBSTANCE = 'sleep_substance_disclosure';

    public function grant(User $user, string $type, ?string $purpose = null, string $policyVersion = '1.0'): ProfileConsent
    {
        return ProfileConsent::query()->updateOrCreate(
            ['user_id' => $user->id, 'consent_type' => $type],
            [
                'granted' => true,
                'purpose' => $purpose,
                'policy_version' => $policyVersion,
                'granted_at' => now(),
                'revoked_at' => null,
            ]
        );
    }

    public function revoke(User $user, string $type): ProfileConsent
    {
        return ProfileConsent::query()->updateOrCreate(
            ['user_id' => $user->id, 'consent_type' => $type],
            [
                'granted' => false,
                'revoked_at' => now(),
            ]
        );
    }

    public function has(User $user, string $type): bool
    {
        return ProfileConsent::query()
            ->where('user_id', $user->id)
            ->where('consent_type', $type)
            ->where('granted', true)
            ->whereNull('revoked_at')
            ->exists();
    }

    public function ensureDefaults(User $user): void
    {
        $this->grant($user, self::ANALYTICAL_USE, 'Use non-sensitive profile answers for dream analysis');
    }
}
