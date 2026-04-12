<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CltLayup extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'layup_code',
        'revision',
        'status',
        'species_grade',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers(): HasMany
    {
        return $this->hasMany(CltLayer::class, 'layup_id');
    }

    /**
     * Get total thickness from all layers
     */
    public function getTotalThicknessAttribute(): float
    {
        return (float) $this->layers->sum('thickness');
    }

    /**
     * Get ply count (number of layers)
     */
    public function getPlyCountAttribute(): int
    {
        return $this->layers->count();
    }

    /**
     * Get species/grade
     */
    public function getSpeciesGradeAttribute(): string
    {
        return $this->attributes['species_grade'] ?? 'N/A';
    }
}
