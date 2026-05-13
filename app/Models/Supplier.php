<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'address',
    ];

    /**
     * Relasi one-to-many ke Layup.
     * Satu supplier bisa memiliki banyak layup.
     * 
     * @return HasMany<Layup, $this>
     */
    public function layups(): HasMany
    {
        return $this->hasMany(Layup::class);
    }
}
