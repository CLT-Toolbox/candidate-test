<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        private SupplierService $supplierService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Supplier::class);

        $sort = request('sort', 'created');
        $perPage = request('per_page', 10);

        $perPage = in_array($perPage, [5, 10, 15, 20]) ? $perPage : 10;

        $suppliers = $this->supplierService->paginate($perPage, $sort);

        return view('suppliers.index', compact('suppliers', 'perPage'));
    }

    public function show(Supplier $supplier): View
    {
        $this->authorize('view', $supplier);
        $supplier->load('layups.layers');
        return view('suppliers.show', compact('supplier'));
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->authorize('create', Supplier::class);
        $this->supplierService->create($request->validated());
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $this->supplierService->update($supplier, $request->validated());
        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);
        $this->supplierService->delete($supplier);
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
