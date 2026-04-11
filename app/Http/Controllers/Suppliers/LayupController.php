<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\StoreLayupRequest;
use App\Http\Requests\Suppliers\UpdateLayupRequest;
use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\LayupRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LayupController extends Controller
{
    public function __construct(
        private readonly LayupRepositoryInterface $layups,
    ) {
    }

    public function create(Supplier $supplier): View
    {
        $this->authorize('create', [Layup::class, $supplier]);

        return view('layups.create', [
            'supplier' => $supplier,
        ]);
    }

    public function store(StoreLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $layup = $this->layups->createForSupplier($supplier, $request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', "Layup [{$layup->name}] created successfully.");
    }

    public function edit(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('update', $layup);

        return view('layups.edit', [
            'supplier' => $supplier,
            'layup' => $layup,
        ]);
    }

    public function update(UpdateLayupRequest $request, Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->layups->update($layup, $request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Layup updated successfully.');
    }

    public function destroy(Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('delete', $layup);

        $this->layups->delete($layup);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Layup deleted successfully.');
    }
}
