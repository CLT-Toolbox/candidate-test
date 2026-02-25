<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CltLayup extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }


    /**
     * Get the layers for the layup.
     */
    public function layers()
    {
        return $this->hasMany(CltLayer::class, 'layup_id')->orderBy('layer_order');
    }

    /**
     * Get the total ply count (calculated from layers).
     */
    public function getPlyCountAttribute()
    {
        return $this->layers()->count();
    }

    /**
     * Get the total thickness (calculated from layers).
     */
    public function getTotalThicknessAttribute()
    {
        return $this->layers()->sum('thickness');
    }
}
