<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'development_theme_id',
        'user_id',
        'note',
        'progress_score',
    ];
}
