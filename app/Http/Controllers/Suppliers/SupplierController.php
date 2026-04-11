<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\StoreSupplierRequest;
use App\Http\Requests\Suppliers\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\Suppliers\SupplierImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierRepositoryInterface $suppliers,
    ) {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Supplier::class);

        return view('suppliers.index', [
            'suppliers' => $this->suppliers->paginateWithCounts(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Supplier::class);

        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $supplier = $this->suppliers->create($request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier): View
    {
        $this->authorize('view', $supplier);

        $supplier = $this->suppliers->findWithRelations($supplier);

        return view('suppliers.show', [
            'supplier' => $supplier,
            'importStrategies' => [
                SupplierImportService::STRATEGY_MANUAL => 'Manual review',
                SupplierImportService::STRATEGY_OVERWRITE => 'Overwrite existing',
                SupplierImportService::STRATEGY_SKIP => 'Skip conflicts',
                SupplierImportService::STRATEGY_REJECT => 'Reject entire import',
            ],
            'hasPendingConflictResolution' => session()->has($this->pendingImportPreviewKey($supplier)),
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        $this->authorize('update', $supplier);

        return view('suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->suppliers->update($supplier, $request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);

        $this->suppliers->delete($supplier);
        session()->forget($this->pendingImportPreviewKey($supplier));

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    private function pendingImportPreviewKey(Supplier $supplier): string
    {
        return 'supplier-import-preview.'.$supplier->id;
    }
}
