<?php

namespace App\Services\Profile;

use App\Models\ProfileObservation;
use App\Models\User;

class ProfileObservationService
{
    public function createHypothesis(User $user, string $observation, ?string $dimension = null, array $evidence = [], ?string $confidence = 'low'): ProfileObservation
    {
        return ProfileObservation::query()->create([
            'user_id' => $user->id,
            'dimension' => $dimension,
            'observation' => $observation,
            'evidence_json' => $evidence,
            'source_type' => 'ai_hypothesis',
            'status' => 'pending',
            'confidence_level' => $confidence,
            'user_confirmation_status' => 'pending',
            'first_observed_at' => now(),
            'last_observed_at' => now(),
        ]);
    }

    public function confirm(ProfileObservation $observation): ProfileObservation
    {
        $observation->update([
            'status' => 'confirmed',
            'user_confirmation_status' => 'confirmed',
            'source_type' => 'user_confirmed',
            'last_observed_at' => now(),
        ]);

        return $observation->fresh();
    }

    public function reject(ProfileObservation $observation): ProfileObservation
    {
        $observation->update([
            'status' => 'rejected',
            'user_confirmation_status' => 'rejected',
            'last_observed_at' => now(),
        ]);

        return $observation->fresh();
    }

    /**
     * @return \Illuminate\Support\Collection<int, ProfileObservation>
     */
    public function confirmedForContext(User $user)
    {
        return ProfileObservation::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed'])
            ->whereIn('source_type', ['user_reported', 'user_confirmed'])
            ->latest('last_observed_at')
            ->limit(30)
            ->get();
    }
}
