<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
   public function layups()
{
    return $this->hasMany(Layup::class);
}
protected $fillable = ['name'];
}
