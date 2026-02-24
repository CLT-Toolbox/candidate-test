<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CltLayup extends Model
{
    protected $fillable = [
        'name',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function cltLayers(): HasMany

    {
        return $this->hasMany(CltLayer::class, 'layup_id', 'id');
    }
}
