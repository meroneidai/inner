<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DreamEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'dream_date',
        'dream_time',
        'title',
        'original_text',
        'corrected_text',
        'sleep_quality',
        'emotional_intensity',
        'mood_upon_waking',
        'personal_notes',
        'status',
        'processing_status',
        'entry_method',
        'language',
    ];

    protected function casts(): array
    {
        return [
            'dream_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DreamEntry $dream): void {
            if (empty($dream->uuid)) {
                $dream->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function displayText(): string
    {
        return (string) ($this->corrected_text ?: $this->original_text ?: '');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function audioFiles(): HasMany
    {
        return $this->hasMany(DreamAudioFile::class);
    }

    public function transcriptions(): HasMany
    {
        return $this->hasMany(DreamTranscription::class);
    }

    public function latestTranscription(): HasOne
    {
        return $this->hasOne(DreamTranscription::class)->latestOfMany();
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(DreamAnalysis::class);
    }

    public function latestAnalysis(): HasOne
    {
        return $this->hasOne(DreamAnalysis::class)->latestOfMany();
    }

    public function events(): HasMany
    {
        return $this->hasMany(DreamEvent::class);
    }

    public function symbolOccurrences(): HasMany
    {
        return $this->hasMany(DreamSymbolOccurrence::class);
    }

    public function emotions(): HasMany
    {
        return $this->hasMany(DreamEmotion::class);
    }
}
