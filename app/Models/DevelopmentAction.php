<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'development_theme_id',
        'user_id',
        'dream_analysis_id',
        'cadence',
        'title',
        'instructions',
        'status',
        'due_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(DevelopmentTheme::class, 'development_theme_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(DreamAnalysis::class, 'dream_analysis_id');
    }

    public function cadenceLabel(): string
    {
        return match ($this->cadence) {
            'daily' => __('اليوم'),
            'weekly' => __('هذا الأسبوع'),
            'monthly' => __('هذا الشهر'),
            default => $this->cadence,
        };
    }

    public function cadenceEmoji(): string
    {
        return match ($this->cadence) {
            'daily' => '☀️',
            'weekly' => '📅',
            'monthly' => '🌙',
            default => '🌱',
        };
    }
}
