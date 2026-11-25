<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DreamSymbolOccurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'dream_entry_id',
        'dream_analysis_id',
        'dream_symbol_id',
        'context',
        'interpretations',
        'interpretation_confidence',
        'evidence_level',
    ];

    protected function casts(): array
    {
        return [
            'interpretations' => 'array',
            'interpretation_confidence' => 'float',
        ];
    }

    public function symbol(): BelongsTo
    {
        return $this->belongsTo(DreamSymbol::class, 'dream_symbol_id');
    }

    public function dream(): BelongsTo
    {
        return $this->belongsTo(DreamEntry::class, 'dream_entry_id');
    }
}
