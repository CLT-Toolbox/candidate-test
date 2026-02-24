<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CltLayers extends Model
{
    protected $guarded = ['id'];
    protected $table = 'clt_layers';

    public function layup()
    {
        return $this->belongsTo(CltLayups::class, 'layup_id');
    }
}
