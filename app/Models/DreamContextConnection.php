<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DreamContextConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'dream_analysis_id',
        'related_dream_entry_id',
        'connection_type',
        'description',
    ];
}
