<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dimension',
        'observation',
        'evidence_json',
        'source_type',
        'source_id',
        'status',
        'confidence_level',
        'user_confirmation_status',
        'first_observed_at',
        'last_observed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'evidence_json' => 'array',
            'first_observed_at' => 'datetime',
            'last_observed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isConfirmed(): bool
    {
        return $this->user_confirmation_status === 'confirmed' || $this->status === 'confirmed';
    }
}
