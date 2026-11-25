<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_name',
        'preferred_language',
        'date_of_birth',
        'age_range',
        'gender',
        'cultural_background',
        'country_of_origin',
        'country_of_residence',
        'religious_spiritual_background',
        'interpretive_perspectives',
        'growth_goals',
        'emotional_state',
        'sleep_quality',
        'life_circumstances',
        'profile_status',
        'onboarding_stage',
        'current_section_slug',
        'profile_last_reviewed_at',
        'completion_percent',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'interpretive_perspectives' => 'array',
            'growth_goals' => 'array',
            'profile_last_reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
