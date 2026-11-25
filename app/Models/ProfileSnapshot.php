<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'version_number',
        'profile_summary',
        'structured_profile_json',
        'generated_by',
        'source_answer_ids',
    ];

    protected function casts(): array
    {
        return [
            'structured_profile_json' => 'array',
            'source_answer_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
