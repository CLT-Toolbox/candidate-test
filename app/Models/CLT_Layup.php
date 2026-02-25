<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CLT_Layup extends Model
{
    use HasFactory;

    protected $table = 'c_l_t__layups';
    protected $fillable = ['supplier_id', 'name'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers(): HasMany
    {
        return $this->hasMany(CLT_Layer::class, 'layup_id');
    }
}

