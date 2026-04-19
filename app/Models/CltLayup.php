<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CltLayup extends Model
{
    use HasFactory;

    protected $table = 'clt_layups';

    protected $fillable = [
        'supplier_id',
        'name'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers()
    {
        return $this->hasMany(CltLayer::class, 'layup_id');
    }
}
