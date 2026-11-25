<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileQuestionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'locale',
        'question_text',
        'description',
        'options_json',
    ];

    protected function casts(): array
    {
        return [
            'options_json' => 'array',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProfileQuestion::class, 'question_id');
    }
}
