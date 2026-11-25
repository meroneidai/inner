<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DreamAnalysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'dream_entry_id',
        'user_id',
        'prompt_version_id',
        'status',
        'provider',
        'model',
        'payload',
        'summary',
        'analysis_confidence',
        'schema_version',
        'error_message',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'analysis_confidence' => 'float',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DreamAnalysis $analysis): void {
            if (empty($analysis->uuid)) {
                $analysis->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function dream(): BelongsTo
    {
        return $this->belongsTo(DreamEntry::class, 'dream_entry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promptVersion(): BelongsTo
    {
        return $this->belongsTo(AnalysisPromptVersion::class, 'prompt_version_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(AnalysisFeedback::class);
    }

    public function culturalInterpretations(): HasMany
    {
        return $this->hasMany(DreamCulturalInterpretation::class);
    }

    public function contextConnections(): HasMany
    {
        return $this->hasMany(DreamContextConnection::class);
    }
}
