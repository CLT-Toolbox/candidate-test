<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CltLayups extends Model
{
    protected $guarded = ['id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers()
    {
        return $this->hasMany(CltLayers::class, 'layup_id')->orderBy('layer_order');
    }
}
