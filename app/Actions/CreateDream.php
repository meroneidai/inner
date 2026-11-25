<?php

namespace App\Actions;

use App\Models\DreamEmotion;
use App\Models\DreamEntry;
use App\Models\User;

class CreateDream
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): DreamEntry
    {
        $dream = $user->dreams()->create([
            'dream_date' => $data['dream_date'] ?? now()->toDateString(),
            'dream_time' => $data['dream_time'] ?? null,
            'title' => $data['title'] ?? null,
            'original_text' => $data['original_text'] ?? null,
            'corrected_text' => $data['corrected_text'] ?? ($data['original_text'] ?? null),
            'sleep_quality' => $data['sleep_quality'] ?? null,
            'emotional_intensity' => $data['emotional_intensity'] ?? null,
            'mood_upon_waking' => $data['mood_upon_waking'] ?? null,
            'personal_notes' => $data['personal_notes'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'processing_status' => 'idle',
            'entry_method' => $data['entry_method'] ?? 'text',
            'language' => $data['language'] ?? ($user->preferences?->locale ?? 'ar'),
        ]);

        if (! empty($data['mood_upon_waking'])) {
            DreamEmotion::query()->create([
                'dream_entry_id' => $dream->id,
                'emotion' => $data['mood_upon_waking'],
                'source' => 'user',
                'intensity' => $data['emotional_intensity'] ?? null,
            ]);
        }

        return $dream;
    }
}
