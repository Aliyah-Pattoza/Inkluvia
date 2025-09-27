<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialBrailleContent extends Model
{
    protected $fillable = [
        'material_id',
        'page_number',
        'braille_text',
        'original_text',
        'metadata',
        'line_count',
        'character_count'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
