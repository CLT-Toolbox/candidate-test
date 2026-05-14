<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layer extends Model
{
    /** @use HasFactory<\Database\Factories\LayerFactory> */
    use HasFactory;

    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
        'grade',
    ];

    protected $casts = [
        'thickness' => 'decimal:4',
        'width' => 'decimal:4',
        'angle' => 'decimal:2',
    ];

    public function layup(): BelongsTo
    {
        return $this->belongsTo(Layup::class, 'layup_id', 'layup_id');
    }
}
