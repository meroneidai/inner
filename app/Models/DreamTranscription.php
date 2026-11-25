<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DreamTranscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'dream_entry_id',
        'dream_audio_file_id',
        'original_transcription',
        'edited_transcription',
        'detected_language',
        'confidence',
        'provider',
        'status',
        'error_message',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'float',
            'confirmed_at' => 'datetime',
        ];
    }

    public function dreamEntry(): BelongsTo
    {
        return $this->belongsTo(DreamEntry::class);
    }
}
