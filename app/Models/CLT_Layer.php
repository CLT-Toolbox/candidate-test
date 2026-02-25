<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CLT_Layer extends Model
{
    use HasFactory;

    protected $table = 'c_l_t__layers';
    protected $fillable = ['layup_id', 'layer_order', 'thickness', 'width', 'angle'];

    public function layup(): BelongsTo
    {
        return $this->belongsTo(CLT_Layup::class, 'layup_id');
    }
}

