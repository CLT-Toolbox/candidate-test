<?php

namespace App\Http\Controllers;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Http\Request;

class LayersController extends Controller
{
    /**
     * Display a listing of the layers for a specific layup.
     */
    public function index(CltLayup $layup)
    {
        $supplier = $layup->supplier;

        $layers = CltLayer::where('layup_id', $layup->id)
            ->ordered()
            ->paginate(10);

        return view('layers.index', compact('layup', 'supplier', 'layers'));
    }

    /**
     * Store a newly created layer in storage.
     */
    public function store(Request $request, CltLayup $layup)
    {
        $validated = $request->validate([
            'layer_order' => 'required|integer|min:1',
            'thickness' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'angle' => 'required|numeric|min:-360|max:360',
        ]);

        $validated['layup_id'] = $layup->id;

        CltLayer::create($validated);

        return redirect()
            ->route('layups.layers.index', $layup->id)
            ->with('success', 'Layer added successfully.');
    }

    /**
     * Update the specified layer in storage.
     */
    public function update(Request $request, CltLayup $layup, CltLayer $layer)
    {
        // Ensure the layer belongs to the layup
        if ($layer->layup_id != $layup->id) {
            abort(404);
        }

        $validated = $request->validate([
            'layer_order' => 'required|integer|min:1',
            'thickness' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'angle' => 'required|numeric|min:-360|max:360',
        ]);

        $layer->update($validated);

        return redirect()
            ->route('layups.layers.index', $layup->id)
            ->with('success', 'Layer updated successfully.');
    }

    /**
     * Remove the specified layer from storage.
     */
    public function destroy(CltLayup $layup, CltLayer $layer)
    {
        // Ensure the layer belongs to the layup
        if ($layer->layup_id != $layup->id) {
            abort(404);
        }

        $layer->delete();

        return redirect()
            ->route('layups.layers.index', $layup->id)
            ->with('success', 'Layer deleted successfully.');
    }
}
