<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'dream_analysis_id',
        'user_id',
        'relevance',
        'accuracy_rating',
        'will_try_action',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'will_try_action' => 'boolean',
            'accuracy_rating' => 'integer',
        ];
    }
}
