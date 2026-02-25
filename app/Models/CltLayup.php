<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CltLayup extends Model
{
    protected $fillable = ['supplier_id', 'name'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers(): HasMany
    {
        return $this->hasMany(CltLayer::class)->orderBy('layer_order');
    }
}