<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model

{

    use HasFactory;
    protected $fillable = ['name'];

    public function layups()
    {
        return $this->hasMany(Layup::class);
    }

    public function getCodeAttribute()
    {
        $year = $this->created_at->format('Y');
        $number = str_pad($this->id, 4, '0', STR_PAD_LEFT);

        return "SUP-{$year}-{$number}";
    }
}
