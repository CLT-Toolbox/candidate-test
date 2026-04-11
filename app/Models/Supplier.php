<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'name',
    ];

    public function layups()
    {
        return $this->hasMany(CltLayup::class, 'supplier_id');
    }
}
