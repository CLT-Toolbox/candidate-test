<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayup\StoreCltLayupRequest;
use App\Http\Requests\CltLayup\UpdateCltLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CltLayupController extends Controller
{
    public function __construct(
        private CltLayupService $layupService
    ) {}

    public function show(Supplier $supplier, CltLayup $layup): View
    {
        $this->authorize('view', $layup);
        $layup->load('layers');
        return view('layups.show', compact('supplier', 'layup'));
    }

    public function store(StoreCltLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('create', CltLayup::class);
        $layup = $this->layupService->create(array_merge($request->validated(), ['supplier_id' => $supplier->id]));

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layup created successfully.');
    }

    public function update(UpdateCltLayupRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('update', $layup);
        $this->layupService->update($layup, $request->validated());

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layup updated successfully.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('delete', $layup);
        $this->layupService->delete($layup);
        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layup deleted successfully.');
    }
}
