<?php

namespace App\Models\Content;

use Database\Factories\Content\ReferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reference extends Model
{
    /** @use HasFactory<ReferenceFactory> */
    use HasFactory;

    protected $table = 'content_references';

    protected $fillable = [
        'article_id',
        'label',
        'url',
        'source_type',
        'citation',
        'sort_order',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
