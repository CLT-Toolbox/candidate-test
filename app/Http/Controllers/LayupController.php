<?php

namespace App\Http\Controllers;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LayupController extends Controller
{
    public function index(Supplier $supplier)
    {
        $supplier->load('layups.layers');
        return view('layups.index', compact('supplier'));
    }

    public function create(Supplier $supplier)
    {
        return view('layups.create', compact('supplier'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:clt_layups,name,NULL,id,supplier_id,' . $supplier->id,
        ]);

        $layup = $supplier->layups()->create($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layup created successfully.');
    }

    public function show(Supplier $supplier, Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }
        $layup->load('layers');
        return view('layups.show', compact('supplier', 'layup'));
    }

    public function edit(Supplier $supplier, Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }
        return view('layups.edit', compact('supplier', 'layup'));
    }

    public function update(Request $request, Supplier $supplier, Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:clt_layups,name,' . $layup->id . ',id,supplier_id,' . $supplier->id,
        ]);

        $layup->update($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layup updated successfully.');
    }

    public function destroy(Supplier $supplier, Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }
        
        $layup->delete();

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layup deleted successfully.');
    }
}
