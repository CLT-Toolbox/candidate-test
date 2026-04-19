<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CltLayer extends Model
{
    use HasFactory;
    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
    ];

    public function layup()
    {
        return $this->belongsTo(CltLayup::class, 'layup_id', 'id');
    }
}
