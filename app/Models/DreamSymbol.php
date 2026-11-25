<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DreamSymbol extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'normalized_key',
        'description',
    ];

    public function occurrences(): HasMany
    {
        return $this->hasMany(DreamSymbolOccurrence::class);
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }
}
