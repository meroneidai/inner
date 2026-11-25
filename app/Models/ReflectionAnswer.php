<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReflectionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dream_analysis_id',
        'development_action_id',
        'question',
        'answer',
    ];
}
