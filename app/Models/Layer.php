<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Layer extends Model
{
    use HasFactory;

    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle'
    ];

    public function layup()
    {
        return $this->belongsTo(Layup::class);
    }
}
