<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCltLayerRequest;
use App\Http\Requests\UpdateCltLayerRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CltLayerController extends Controller
{
    public function index(Supplier $supplier, CltLayup $layup): View
    {
        $this->authorize('view', $supplier);
        $this->authorize('view', $layup);

        abort_unless($layup->supplier_id === $supplier->id, 404);

        $layers = $layup->cltLayers()->paginate(20);

        return view('clt_layers.index', compact('supplier', 'layup', 'layers'));
    }

    public function create(Supplier $supplier, CltLayup $layup): View
    {
        $this->authorize('update', $supplier);
        $this->authorize('update', $layup);

        abort_unless($layup->supplier_id === $supplier->id, 404);

        return view('clt_layers.create', compact('supplier', 'layup'));
    }

    public function store(StoreCltLayerRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $this->authorize('update', $layup);

        abort_unless($layup->supplier_id === $supplier->id, 404);

        $data = $request->validated();
        if ($layup->cltLayers()->where('layer_order', $data['layer_order'])->exists()) {
            throw ValidationException::withMessages(['layer_order' => 'This layer order is already used in this layup.']);
        }

        $layup->cltLayers()->create($data);

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])->with('status', 'Layer created.');
    }

    public function show(Supplier $supplier, CltLayup $layup, CltLayer $layer): View
    {
        $this->authorize('view', $supplier);
        $this->authorize('view', $layup);
        $this->authorize('view', $layer);

        abort_unless($layup->supplier_id === $supplier->id && $layer->clt_layup_id === $layup->id, 404);

        return view('clt_layers.show', compact('supplier', 'layup', 'layer'));
    }

    public function edit(Supplier $supplier, CltLayup $layup, CltLayer $layer): View
    {
        $this->authorize('update', $supplier);
        $this->authorize('update', $layup);
        $this->authorize('update', $layer);

        abort_unless($layup->supplier_id === $supplier->id && $layer->clt_layup_id === $layup->id, 404);

        return view('clt_layers.edit', compact('supplier', 'layup', 'layer'));
    }

    public function update(UpdateCltLayerRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $this->authorize('update', $layup);
        $this->authorize('update', $layer);

        abort_unless($layup->supplier_id === $supplier->id && $layer->clt_layup_id === $layup->id, 404);

        $data = $request->validated();
        if ((int) $data['layer_order'] !== (int) $layer->layer_order
            && $layup->cltLayers()->where('layer_order', $data['layer_order'])->exists()) {
            throw ValidationException::withMessages(['layer_order' => 'This layer order is already used in this layup.']);
        }

        $layer->update($data);

        return redirect()->route('suppliers.layups.layers.show', [$supplier, $layup, $layer])->with('status', 'Layer updated.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $this->authorize('update', $layup);
        $this->authorize('delete', $layer);

        abort_unless($layup->supplier_id === $supplier->id && $layer->clt_layup_id === $layup->id, 404);

        $layer->delete();

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])->with('status', 'Layer deleted.');
    }
}
