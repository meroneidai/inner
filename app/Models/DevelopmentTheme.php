<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentTheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'why_suggested',
        'status',
        'review_date',
        'related_dream_ids',
    ];

    protected function casts(): array
    {
        return [
            'review_date' => 'date',
            'related_dream_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(DevelopmentAction::class);
    }

    public function progressNotes(): HasMany
    {
        return $this->hasMany(DevelopmentProgress::class);
    }
}
