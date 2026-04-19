<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCltLayupRequest;
use App\Http\Requests\UpdateCltLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CltLayupController extends Controller
{
    public function index(Supplier $supplier): View
    {
        $this->authorize('view', $supplier);

        $layups = $supplier->cltLayups()->orderBy('name')->withCount('cltLayers')->paginate(15);

        return view('clt_layups.index', compact('supplier', 'layups'));
    }

    public function create(Supplier $supplier): View
    {
        $this->authorize('update', $supplier);

        return view('clt_layups.create', compact('supplier'));
    }

    public function store(StoreCltLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);

        $data = $request->validated();
        if ($supplier->cltLayups()->where('name', $data['name'])->exists()) {
            throw ValidationException::withMessages(['name' => 'A layup with this name already exists for this supplier.']);
        }

        $layup = $supplier->cltLayups()->create($data);

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])->with('status', 'Layup created.');
    }

    public function show(Supplier $supplier, CltLayup $layup): View
    {
        $this->authorize('view', $supplier);
        $this->authorize('view', $layup);

        abort_unless($layup->supplier_id === $supplier->id, 404);

        $layup->load('cltLayers');

        return view('clt_layups.show', compact('supplier', 'layup'));
    }

    public function edit(Supplier $supplier, CltLayup $layup): View
    {
        $this->authorize('update', $supplier);
        $this->authorize('update', $layup);

        abort_unless($layup->supplier_id === $supplier->id, 404);

        return view('clt_layups.edit', compact('supplier', 'layup'));
    }

    public function update(UpdateCltLayupRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $this->authorize('update', $layup);

        abort_unless($layup->supplier_id === $supplier->id, 404);

        $data = $request->validated();
        if ($data['name'] !== $layup->name && $supplier->cltLayups()->where('name', $data['name'])->exists()) {
            throw ValidationException::withMessages(['name' => 'A layup with this name already exists for this supplier.']);
        }

        $layup->update($data);

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])->with('status', 'Layup updated.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $this->authorize('delete', $layup);

        abort_unless($layup->supplier_id === $supplier->id, 404);

        $layup->delete();

        return redirect()->route('suppliers.layups.index', $supplier)->with('status', 'Layup deleted.');
    }
}
