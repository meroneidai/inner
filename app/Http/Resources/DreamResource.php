<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\DreamEntry */
class DreamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'dream_date' => $this->dream_date?->toDateString(),
            'dream_time' => $this->dream_time,
            'text' => $this->displayText(),
            'original_text' => $this->original_text,
            'corrected_text' => $this->corrected_text,
            'sleep_quality' => $this->sleep_quality,
            'emotional_intensity' => $this->emotional_intensity,
            'mood_upon_waking' => $this->mood_upon_waking,
            'personal_notes' => $this->personal_notes,
            'status' => $this->status,
            'processing_status' => $this->processing_status,
            'entry_method' => $this->entry_method,
            'language' => $this->language,
            'latest_analysis' => $this->whenLoaded('latestAnalysis'),
            'latest_transcription' => $this->whenLoaded('latestTranscription'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
