<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layup extends Model
{
    /** @use HasFactory<\Database\Factories\LayupFactory> */
    use HasFactory;

    protected $primaryKey = 'layup_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'DRAFT';
    const STATUS_ARCHIVED = 'ARCHIVED';

    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Draft',
        self::STATUS_ARCHIVED => 'Archived',
    ];

    protected $fillable = [
        'supplier_id',
        'layup_id',
        'name',
        'description',
        'thickness',
        'grade',
        'status',
    ];

    public static function generateLayupId(): string
    {
        $lastLayup = static::orderByRaw('CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(layup_id, "-", 2), "-", -1) AS UNSIGNED) DESC')
            ->first();

        if ($lastLayup) {
            $lastNumber = (int) preg_replace('/[^0-9]/', '', explode('-', $lastLayup->layup_id)[1]);
            $lastLetter = substr($lastLayup->layup_id, -1);

            if ($lastLetter === 'Z') {
                $newNumber = $lastNumber + 1;
                $newLetter = 'A';
            } else {
                $newNumber = $lastNumber;
                $newLetter = chr(ord($lastLetter) + 1);
            }
        } else {
            $newNumber = 204;
            $newLetter = 'A';
        }

        $layupId = 'L-' . $newNumber . '-' . $newLetter;

        while (static::where('layup_id', $layupId)->exists()) {
            if ($newLetter === 'Z') {
                $newNumber++;
                $newLetter = 'A';
            } else {
                $newLetter = chr(ord($newLetter) + 1);
            }
            $layupId = 'L-' . $newNumber . '-' . $newLetter;
        }

        return $layupId;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function layers(): HasMany
    {
        return $this->hasMany(Layer::class, 'layup_id', 'layup_id');
    }
}
