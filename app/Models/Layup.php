<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layup extends Model
{
    protected $fillable = ['name', 'supplier_id'];
    public function supplier()
{
    return $this->belongsTo(Supplier::class);
}

public function layers()
{
    return $this->hasMany(Layer::class);
}
}
