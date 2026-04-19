<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function layups()
    {
        return $this->hasMany(CltLayup::class, 'supplier_id', 'id');
    }
}
