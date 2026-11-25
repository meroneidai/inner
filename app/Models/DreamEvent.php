<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DreamEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'dream_entry_id',
        'dream_analysis_id',
        'sequence_order',
        'event_type',
        'description',
        'entities',
    ];
}
