<?php

namespace App\Http\Controllers;

use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LayupController extends Controller
{
    public function index(Supplier $supplier)
    {
        $layups = $supplier->layups()->with('layers')->latest()->get();
        return view('layups.index', compact('supplier', 'layups'));
    }

    public function create(Supplier $supplier)
    {
        return view('layups.create', compact('supplier'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $supplier->layups()->create($validated);
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup created!');
    }

    public function show(CltLayup $layup)
    {
        $layup->load('layers');
        return view('layups.show', compact('layup'));
    }

    public function edit(CltLayup $layup)
    {
        return view('layups.edit', compact('layup'));
    }

    public function update(Request $request, CltLayup $layup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $layup->update($validated);
        return redirect()->route('layups.show', $layup)->with('success', 'Layup updated!');
    }

    public function destroy(CltLayup $layup)
    {
        $supplier = $layup->supplier;
        $layup->delete();
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup deleted!');
    }
}