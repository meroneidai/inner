<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'display_order',
        'stage',
        'is_sensitive',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_sensitive' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProfileQuestion::class, 'section_id')->orderBy('display_order');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        return $locale === 'ar' ? $this->description_ar : $this->description_en;
    }
}
