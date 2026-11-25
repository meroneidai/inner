<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DreamEmotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'dream_entry_id',
        'dream_analysis_id',
        'emotion',
        'source',
        'intensity',
        'context',
    ];

    public function dream(): BelongsTo
    {
        return $this->belongsTo(DreamEntry::class, 'dream_entry_id');
    }
}
