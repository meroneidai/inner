<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'locale',
        'timezone',
        'email_notifications',
        'in_app_notifications',
        'daily_care_enabled',
        'daily_reminder_enabled',
        'analysis_ready_enabled',
        'growth_reminder_enabled',
        'daily_care_hour',
        'daily_reminder_hour',
        'weekly_report_enabled',
        'monthly_report_enabled',
        'dashboard_widgets',
        'allow_ai_training_consent',
        'share_anonymized_insights',
    ];

    protected function casts(): array
    {
        return [
            'email_notifications' => 'boolean',
            'in_app_notifications' => 'boolean',
            'daily_care_enabled' => 'boolean',
            'daily_reminder_enabled' => 'boolean',
            'analysis_ready_enabled' => 'boolean',
            'growth_reminder_enabled' => 'boolean',
            'daily_care_hour' => 'integer',
            'daily_reminder_hour' => 'integer',
            'weekly_report_enabled' => 'boolean',
            'monthly_report_enabled' => 'boolean',
            'dashboard_widgets' => 'array',
            'allow_ai_training_consent' => 'boolean',
            'share_anonymized_insights' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
