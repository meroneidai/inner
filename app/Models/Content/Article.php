<?php

namespace App\Models\Content;

use Database\Factories\Content\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    protected $table = 'content_articles';

    protected $fillable = [
        'pillar_id',
        'author_id',
        'reviewer_id',
        'slug',
        'locale',
        'title',
        'excerpt',
        'body',
        'seo_title',
        'meta_description',
        'canonical_url',
        'primary_keyword',
        'related_keywords',
        'search_intent',
        'featured_image_path',
        'featured_image_alt',
        'og_image_path',
        'cta_type',
        'cta_label',
        'cta_route',
        'reading_time_minutes',
        'editorial_status',
        'indexable',
        'is_ai_assisted',
        'requires_safety_notice',
        'published_at',
        'updated_content_at',
    ];

    protected function casts(): array
    {
        return [
            'related_keywords' => 'array',
            'indexable' => 'boolean',
            'is_ai_assisted' => 'boolean',
            'requires_safety_notice' => 'boolean',
            'published_at' => 'datetime',
            'updated_content_at' => 'datetime',
        ];
    }

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class, 'pillar_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'reviewer_id');
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')->orderBy('sort_order');
    }

    public function references(): HasMany
    {
        return $this->hasMany(Reference::class, 'article_id')->orderBy('sort_order');
    }

    public function relatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'content_article_relations',
            'article_id',
            'related_article_id'
        )->withPivot('sort_order')->orderByPivot('sort_order');
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query
            ->where('editorial_status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function url(): string
    {
        return route('explore.article', [$this->pillar, $this]);
    }

    public function estimateReadingTime(): int
    {
        $words = str_word_count(strip_tags($this->body));

        return max(1, (int) ceil($words / 180));
    }
}
