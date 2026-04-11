<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\StoreLayerRequest;
use App\Http\Requests\Suppliers\UpdateLayerRequest;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\LayerRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LayerController extends Controller
{
    public function __construct(
        private readonly LayerRepositoryInterface $layers,
    ) {
    }

    public function create(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('create', [Layer::class, $layup]);

        return view('layers.create', [
            'supplier' => $supplier,
            'layup' => $layup,
        ]);
    }

    public function store(StoreLayerRequest $request, Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->layers->createForLayup($layup, $request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Layer created successfully.');
    }

    public function edit(Supplier $supplier, Layup $layup, Layer $layer): View
    {
        $this->authorize('update', $layer);

        return view('layers.edit', [
            'supplier' => $supplier,
            'layup' => $layup,
            'layer' => $layer,
        ]);
    }

    public function update(
        UpdateLayerRequest $request,
        Supplier $supplier,
        Layup $layup,
        Layer $layer,
    ): RedirectResponse {
        $this->layers->update($layer, $request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Layer updated successfully.');
    }

    public function destroy(Supplier $supplier, Layup $layup, Layer $layer): RedirectResponse
    {
        $this->authorize('delete', $layer);

        $this->layers->delete($layer);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Layer deleted successfully.');
    }
}
