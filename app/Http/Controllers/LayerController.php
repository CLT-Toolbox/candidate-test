<?php

namespace App\Http\Controllers;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Http\Request;

class LayerController extends Controller
{
    public function create(CltLayup $layup)
    {
        return view('layers.create', compact('layup'));
    }

    public function store(Request $request, CltLayup $layup)
    {
        $validated = $request->validate([
            'layer_order' => 'required|integer',
            'thickness' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'angle' => 'required|numeric|min:0|max:360'
        ]);

        $layup->layers()->create($validated);
        return redirect()->route('layups.show', $layup)->with('success', 'Layer created!');
    }

    public function edit(CltLayer $layer)
    {
        return view('layers.edit', compact('layer'));
    }

    public function update(Request $request, CltLayer $layer)
    {
        $validated = $request->validate([
            'layer_order' => 'required|integer',
            'thickness' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'angle' => 'required|numeric|min:0|max:360'
        ]);

        $layer->update($validated);
        return redirect()->route('layups.show', $layer->layup)->with('success', 'Layer updated!');
    }

    public function destroy(CltLayer $layer)
    {
        $layup = $layer->layup;
        $layer->delete();
        return redirect()->route('layups.show', $layup)->with('success', 'Layer deleted!');
    }
}