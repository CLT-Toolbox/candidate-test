<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayer\StoreCltLayerRequest;
use App\Http\Requests\CltLayer\UpdateCltLayerRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayerService;
use Illuminate\Http\RedirectResponse;

class CltLayerController extends Controller
{
    public function __construct(
        private CltLayerService $layerService
    ) {}

    public function store(StoreCltLayerRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('create', CltLayer::class);
        $this->layerService->create(array_merge($request->validated(), ['layup_id' => $layup->id]));
        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layer created successfully.');
    }

    public function update(UpdateCltLayerRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->authorize('update', $layer);
        $this->layerService->update($layer, $request->validated());
        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layer updated successfully.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->authorize('delete', $layer);
        $this->layerService->delete($layer);
        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layer deleted successfully.');
    }
}
