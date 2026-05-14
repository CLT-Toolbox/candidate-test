<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayerRequest;
use App\Http\Requests\UpdateLayerRequest;
use App\Models\Layup;
use App\Models\Layer;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LayerController extends Controller
{
    public function index(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('view', $layup);
        $layers = $layup->layers()->orderBy('layer_order')->get();

        return view('layers.index', compact('supplier', 'layup', 'layers'));
    }

    public function create(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('view', $layup);

        return view('layers.create', compact('supplier', 'layup'));
    }

    public function store(StoreLayerRequest $request, Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('view', $layup);

        $validated = $request->validated();
        $validated['layup_id'] = $layup->layup_id;
        Layer::create($validated);

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])->with('success', 'Layer created successfully.');
    }

    public function show(Supplier $supplier, Layup $layup, Layer $layer): View
    {
        $this->authorize('view', $layup);

        return view('layers.show', compact('supplier', 'layup', 'layer'));
    }

    public function edit(Supplier $supplier, Layup $layup, Layer $layer): View
    {
        $this->authorize('view', $layup);

        return view('layers.edit', compact('supplier', 'layup', 'layer'));
    }

    public function update(UpdateLayerRequest $request, Supplier $supplier, Layup $layup, Layer $layer): RedirectResponse
    {
        $this->authorize('view', $layup);

        $layer->update($request->validated());

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])->with('success', 'Layer updated successfully.');
    }

    public function destroy(Supplier $supplier, Layup $layup, Layer $layer): RedirectResponse
    {
        $this->authorize('view', $layup);
        $layer->delete();

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])->with('success', 'Layer deleted successfully.');
    }
}
