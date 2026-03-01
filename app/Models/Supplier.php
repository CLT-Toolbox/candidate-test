<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected static function booted()
    {
        static::addGlobalScope('orderByUpdatedAt', function(Builder $builder){
            $builder->orderBy('updated_at', 'desc');
        });
    }

    public function layups(): HasMany
    {
        return $this->hasMany(CLT_Layup::class);
    }
}

