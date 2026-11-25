<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DreamCulturalInterpretation extends Model
{
    use HasFactory;

    protected $fillable = [
        'dream_analysis_id',
        'tradition',
        'source_label',
        'interpretation',
        'confidence_note',
    ];
}
