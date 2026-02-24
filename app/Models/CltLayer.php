<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CltLayer extends Model
{
    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle'
    ];

    protected $casts = [
        'layer_order' => 'integer',
        'thickness' => 'decimal:2',
        'width' => 'decimal:2',
        'angle' => 'decimal:2',
    ];

    public function cltLayup(): BelongsTo
    {
        return $this->belongsTo(CltLayup::class, 'layup_id', 'id');
    }
}
