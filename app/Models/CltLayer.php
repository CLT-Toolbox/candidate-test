<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CltLayer extends Model
{
    protected $fillable = [
        'layer_order',
        'thickness',
        'width',
        'angle'
    ];

    public function cltLayup(): BelongsTo
    {
        return $this->belongsTo(CltLayup::class);
    }
}
