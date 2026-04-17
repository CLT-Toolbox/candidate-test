<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LayerController extends Controller
{
    public function index(Supplier $supplier, Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }
        $layup->load('layers');
        return view('layers.index', compact('supplier', 'layup'));
    }

    public function create(Supplier $supplier, Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }
        return view('layers.create', compact('supplier', 'layup'));
    }

    public function store(Request $request, Supplier $supplier, Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }

        $validated = $request->validate([
            'layer_order' => 'required|integer|unique:clt_layers,layer_order,NULL,id,layup_id,' . $layup->id,
            'thickness' => 'required|numeric|min:0.0001',
            'width' => 'required|numeric|min:0.0001',
            'angle' => 'required|numeric|min:0|max:360',
        ]);

        $layer = $layup->layers()->create($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layer created successfully.');
    }

    public function show(Supplier $supplier, Layup $layup, Layer $layer)
    {
        if ($layup->supplier_id !== $supplier->id || $layer->layup_id !== $layup->id) {
            abort(404);
        }
        return view('layers.show', compact('supplier', 'layup', 'layer'));
    }

    public function edit(Supplier $supplier, Layup $layup, Layer $layer)
    {
        if ($layup->supplier_id !== $supplier->id || $layer->layup_id !== $layup->id) {
            abort(404);
        }
        return view('layers.edit', compact('supplier', 'layup', 'layer'));
    }

    public function update(Request $request, Supplier $supplier, Layup $layup, Layer $layer)
    {
        if ($layup->supplier_id !== $supplier->id || $layer->layup_id !== $layup->id) {
            abort(404);
        }

        $validated = $request->validate([
            'layer_order' => 'required|integer|unique:clt_layers,layer_order,' . $layer->id . ',id,layup_id,' . $layup->id,
            'thickness' => 'required|numeric|min:0.0001',
            'width' => 'required|numeric|min:0.0001',
            'angle' => 'required|numeric|min:0|max:360',
        ]);

        $layer->update($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layer updated successfully.');
    }

    public function destroy(Supplier $supplier, Layup $layup, Layer $layer)
    {
        if ($layup->supplier_id !== $supplier->id || $layer->layup_id !== $layup->id) {
            abort(404);
        }

        $layer->delete();

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layer deleted successfully.');
    }
}
