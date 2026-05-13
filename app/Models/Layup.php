<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layup extends Model
{
    protected $fillable = ['supplier_id','name'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers()
    {
        return $this->hasMany(Layer::class);
    }
}
