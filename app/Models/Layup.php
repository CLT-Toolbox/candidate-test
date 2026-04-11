<?php

namespace App\Models;

use Database\Factories\LayupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layup extends Model
{
    /** @use HasFactory<LayupFactory> */
    use HasFactory;

    protected $table = 'clt_layups';

    protected $fillable = [
        'supplier_id',
        'name',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers(): HasMany
    {
        return $this->hasMany(Layer::class)->orderBy('layer_order');
    }
}
