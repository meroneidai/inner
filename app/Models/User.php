<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'onboarding_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function dreams(): HasMany
    {
        return $this->hasMany(DreamEntry::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(DreamAnalysis::class);
    }

    public function weeklyReports(): HasMany
    {
        return $this->hasMany(WeeklyReport::class);
    }

    public function monthlyReports(): HasMany
    {
        return $this->hasMany(MonthlyReport::class);
    }

    public function developmentThemes(): HasMany
    {
        return $this->hasMany(DevelopmentTheme::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }

    public function profileAnswers(): HasMany
    {
        return $this->hasMany(ProfileAnswer::class);
    }

    public function profileConsents(): HasMany
    {
        return $this->hasMany(ProfileConsent::class);
    }

    public function profileSnapshots(): HasMany
    {
        return $this->hasMany(ProfileSnapshot::class);
    }

    public function profileObservations(): HasMany
    {
        return $this->hasMany(ProfileObservation::class);
    }

    public function profileGoals(): HasMany
    {
        return $this->hasMany(ProfileGoal::class);
    }

    public function profileReviewSessions(): HasMany
    {
        return $this->hasMany(ProfileReviewSession::class);
    }
}
