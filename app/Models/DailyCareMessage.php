<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCareMessage extends Model
{
    protected $fillable = [
        'user_id',
        'care_date',
        'emoji',
        'title',
        'body',
        'tone',
        'focus',
        'source',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'care_date' => 'date',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
