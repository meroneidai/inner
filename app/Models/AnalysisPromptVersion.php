<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisPromptVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'version',
        'system_prompt',
        'user_prompt_template',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }
}
