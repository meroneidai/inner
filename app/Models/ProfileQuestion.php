<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'question_key',
        'answer_type',
        'options_json',
        'validation_rules_json',
        'conditional_rules_json',
        'is_required',
        'is_sensitive',
        'is_active',
        'include_in_stage1',
        'display_order',
        'profile_dimension',
        'ai_context_relevance',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'options_json' => 'array',
            'validation_rules_json' => 'array',
            'conditional_rules_json' => 'array',
            'is_required' => 'boolean',
            'is_sensitive' => 'boolean',
            'is_active' => 'boolean',
            'include_in_stage1' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ProfileSection::class, 'section_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProfileQuestionTranslation::class, 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ProfileAnswer::class, 'question_id');
    }

    public function translation(?string $locale = null): ?ProfileQuestionTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', 'ar');
    }

    public function text(?string $locale = null): string
    {
        return (string) ($this->translation($locale)?->question_text ?? $this->question_key);
    }

    public function description(?string $locale = null): ?string
    {
        return $this->translation($locale)?->description;
    }

    public function options(?string $locale = null): array
    {
        $translated = $this->translation($locale)?->options_json;

        return is_array($translated) ? $translated : ($this->options_json ?? []);
    }
}
