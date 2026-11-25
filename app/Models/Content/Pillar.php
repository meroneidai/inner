<?php

namespace App\Models\Content;

use Database\Factories\Content\PillarFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Pillar extends Model
{
    /** @use HasFactory<PillarFactory> */
    use HasFactory;

    protected $table = 'content_pillars';

    protected $fillable = [
        'slug',
        'locale',
        'name',
        'name_en',
        'tagline',
        'excerpt',
        'intro',
        'seo_title',
        'meta_description',
        'og_image_path',
        'featured_image_path',
        'cta_label',
        'cta_route',
        'sort_order',
        'status',
        'indexable',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'indexable' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'pillar_id');
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')->orderBy('sort_order');
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function url(): string
    {
        return route('explore.pillar', $this);
    }
}
