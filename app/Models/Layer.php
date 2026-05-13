<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layer extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
    ];

    /**
     * Relasi many-to-one ke Layup.
     * Setiap layer dimiliki oleh satu layup.
     * 
     * @return BelongsTo<Layup, $this>
     */
    public function layup(): BelongsTo
    {
        return $this->belongsTo(Layup::class);
    }
}
