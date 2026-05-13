<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layup extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'supplier_id',
        'name',
        'description',
    ];

    /**
     * Relasi many-to-one ke Supplier.
     * Setiap layup dimiliki oleh satu supplier.
     * 
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi one-to-many ke Layer.
     * Satu layup bisa memiliki banyak layer.
     * 
     * @return HasMany<Layer, $this>
     */
    public function layers(): HasMany
    {
        return $this->hasMany(Layer::class);
    }
}
