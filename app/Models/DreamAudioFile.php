<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DreamAudioFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dream_entry_id',
        'disk',
        'path',
        'original_filename',
        'mime_type',
        'size_bytes',
        'duration_seconds',
    ];

    public function dreamEntry(): BelongsTo
    {
        return $this->belongsTo(DreamEntry::class);
    }
}
