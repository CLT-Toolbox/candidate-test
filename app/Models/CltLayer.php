<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CltLayer extends Model
{
    use HasFactory;

    protected $table = 'clt_layers';

    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
    ];

    protected $casts = [
        'thickness' => 'decimal:2',
        'width' => 'decimal:2',
        'angle' => 'decimal:1',
    ];

    /**
     * Get the layup that owns the layer.
     */
    public function layup()
    {
        return $this->belongsTo(CltLayup::class, 'layup_id');
    }

    /**
     * Boot the model and add event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Before creating a layer, handle layer order insertion
        static::creating(function ($layer) {
            if ($layer->layer_order) {
                static::reorderOnInsert($layer->layup_id, $layer->layer_order);
            }
        });

        // Before updating a layer, handle layer order changes
        static::updating(function ($layer) {
            if ($layer->isDirty('layer_order')) {
                $oldOrder = $layer->getOriginal('layer_order');
                $newOrder = $layer->layer_order;
                static::reorderOnUpdate($layer->layup_id, $oldOrder, $newOrder, $layer->id);
            }
        });

        // After deleting a layer, reorder remaining layers
        static::deleted(function ($layer) {
            static::reorderOnDelete($layer->layup_id, $layer->layer_order);
        });
    }

    /**
     * Reorder layers when inserting a new layer.
     */
    protected static function reorderOnInsert($layupId, $newOrder)
    {
        // Shift all layers at or after the new position down by 1
        static::where('layup_id', $layupId)
            ->where('layer_order', '>=', $newOrder)
            ->increment('layer_order');
    }

    /**
     * Reorder layers when updating a layer's position.
     * Logic: like a race - when a runner changes position, others shift accordingly.
     */
    protected static function reorderOnUpdate($layupId, $oldOrder, $newOrder, $layerId)
    {
        if ($oldOrder == $newOrder) {
            return; // No change needed
        }

        if ($newOrder < $oldOrder) {
            // Moving up (e.g., from position 3 to position 1)
            // Layers between new and old position shift down
            static::where('layup_id', $layupId)
                ->where('id', '!=', $layerId)
                ->where('layer_order', '>=', $newOrder)
                ->where('layer_order', '<', $oldOrder)
                ->increment('layer_order');
        } else {
            // Moving down (e.g., from position 1 to position 3)
            // Layers between old and new position shift up
            static::where('layup_id', $layupId)
                ->where('id', '!=', $layerId)
                ->where('layer_order', '>', $oldOrder)
                ->where('layer_order', '<=', $newOrder)
                ->decrement('layer_order');
        }
    }

    /**
     * Reorder layers when deleting a layer.
     */
    protected static function reorderOnDelete($layupId, $deletedOrder)
    {
        // Shift all layers after the deleted position up by 1
        static::where('layup_id', $layupId)
            ->where('layer_order', '>', $deletedOrder)
            ->decrement('layer_order');
    }

    /**
     * Scope to order layers by layer_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('layer_order');
    }
}
