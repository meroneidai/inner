<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'question_version',
        'answer_value',
        'answer_metadata_json',
        'excluded_from_ai',
        'is_draft',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'answer_metadata_json' => 'array',
            'excluded_from_ai' => 'boolean',
            'is_draft' => 'boolean',
            'answered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProfileQuestion::class, 'question_id');
    }

    public function decodedValue(): mixed
    {
        $value = $this->answer_value;
        if ($value === null) {
            return null;
        }

        $json = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $json : $value;
    }
}
