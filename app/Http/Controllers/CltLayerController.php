<?php

namespace App\Http\Controllers;

use App\Models\CltLayers;
use App\Models\CltLayups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CltLayerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'layup_id' => 'required|integer|exists:clt_layups,id',
            'layer_order' => 'required|integer|min:1',
            'thickness' => 'required|numeric|min:0.01',
            'width' => 'required|numeric|min:0.01',
            'angle' => 'required|numeric|min:0|max:360',
        ]);
        
        try {
            CltLayers::create($validated);

            return redirect()
                ->back()
                ->with('success', 'Layer added successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to add layer: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to add layer.');
        }
    }

    public function destroy($id) {
        try {
            $layer = CltLayers::findOrFail($id);
            $layup = $layer->layup;
            $layer->delete();

            return redirect()
                ->back()
                ->with('success', 'Layer deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete layer: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to delete layer.');
        }
    }

    public function update(Request $request, $id) {
        try {
            $layup = CltLayups::findOrFail($id);
            $changes = json_decode($request->input('changes', '{}'), true);

            // Handle deleted layers
            if (!empty($changes['deleted'])) {
                CltLayers::whereIn('id', $changes['deleted'])->delete();
            }

            // Handle created layers
            if (!empty($changes['created'])) {
                foreach ($changes['created'] as $newLayer) {
                    // Get next order for this layup
                    $nextOrder = CltLayers::where('layup_id', $layup->id)->max('layer_order') + 1;
                    
                    CltLayers::create([
                        'layup_id' => $layup->id,
                        'layer_order' => $nextOrder,
                        'thickness' => $newLayer['thickness'],
                        'width' => $newLayer['width'],
                        'angle' => $newLayer['angle'],
                    ]);
                }
            }

            // Prepare reordered (to avoid unique collisions)
            $reorderedIds = [];
            if (!empty($changes['reordered'])) {
                $reorderedIds = array_map(function($r){ return (int)$r['id']; }, $changes['reordered']);
            }

            // skip layer_order if it's part of reordered set
            if (!empty($changes['updated'])) {
                foreach ($changes['updated'] as $layerId => $updates) {
                    $layer = CltLayers::find($layerId);
                    if ($layer) {
                        // If this id is in reordered list, do not set layer_order
                        if (isset($updates['layer_order']) && in_array((int)$layerId, $reorderedIds, true)) {
                            unset($updates['layer_order']);
                        }
                        if (!empty($updates)) {
                            $layer->update($updates);
                        }
                    }
                }
            }

            // Handle reordered layers
            if (!empty($changes['reordered'])) {
                $reordered = $changes['reordered'];
                $ids = array_column($reordered, 'id');

                DB::transaction(function() use ($reordered, $ids, $layup) {
                    $cases = "CASE";
                    foreach ($reordered as $r) {
                        $id = (int) $r['id'];
                        $order = (int) $r['layer_order'];
                        $cases .= " WHEN id = $id THEN $order";
                    }
                    $cases .= " END";

                    // assign temporary unique values
                    DB::table('clt_layers')
                        ->whereIn('id', $ids)
                        ->where('layup_id', $layup->id)
                        ->update(['layer_order' => DB::raw('-id')]);

                    // assign final orders
                    DB::table('clt_layers')
                        ->whereIn('id', $ids)
                        ->where('layup_id', $layup->id)
                        ->update(['layer_order' => DB::raw($cases)]);
                });
            }

            return redirect()
                ->back()
                ->with('success', 'All changes saved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to batch update layers: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to save changes: ' . $e->getMessage());
        }
    }
}
