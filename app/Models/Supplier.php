<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    protected $primaryKey = 'supplier_id';
    public $incrementing = false;
    protected $keyType = 'string';

    Const STATUS_ACTIVE = 'ACTIVE';
    Const STATUS_INACTIVE = 'INACTIVE';

    const STATUSES = [
        self::STATUS_ACTIVE => 'Active Partner',
        self::STATUS_INACTIVE => 'Inactive Partner',
    ];

    protected $fillable = [
        'supplier_id',
        'name',
        'email',
        'location',
        'certification',
        'audit_date',
        'status',
    ];

    public function layups(): HasMany
    {
        return $this->hasMany(Layup::class, 'supplier_id', 'supplier_id');
    }

    public static function generateSupplierId(): string
    {
        $year = date('Y');
        $prefix = 'SUP-' . $year . '-';
        
        $lastSupplier = static::where('supplier_id', 'like', $prefix . '%')
            ->orderBy('supplier_id', 'desc')
            ->first();

        if ($lastSupplier) {
            $lastNumber = (int)substr($lastSupplier->supplier_id, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}

