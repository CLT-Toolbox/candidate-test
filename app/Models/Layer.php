<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layer extends Model
{
    use HasFactory;

    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
    ];

    protected function casts(): array
    {
        return [
            'layer_order' => 'integer',
            'thickness' => 'float',
            'width' => 'float',
            'angle' => 'float',
        ];
    }

    public function layup(): BelongsTo
    {
        return $this->belongsTo(Layup::class);
    }
}
