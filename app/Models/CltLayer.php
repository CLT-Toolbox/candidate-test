<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CltLayer extends Model
{
    protected $fillable = [
        'clt_layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
    ];

    protected function casts(): array
    {
        return [
            'layer_order' => 'integer',
            'thickness' => 'decimal:3',
            'width' => 'decimal:3',
            'angle' => 'decimal:3',
        ];
    }

    public function cltLayup(): BelongsTo
    {
        return $this->belongsTo(CltLayup::class);
    }
}
