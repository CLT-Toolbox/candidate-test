<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('layups')->latest()->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Supplier::create($validated);
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier created!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('layups.layers');
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $supplier->update($validated);
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier updated!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier deleted!');
    }

    public function export(Supplier $supplier, ExportService $exportService)
    {
        return $exportService->download($supplier);
    }

    public function importForm()
    {
        return view('suppliers.import');
    }

    public function import(Request $request, ImportService $importService)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:json,txt',
            'conflict_strategy' => 'required|in:overwrite,skip,duplicate,reject'
        ]);

        $fileContent = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($fileContent, true);

        if (!$data) {
            return back()->withErrors(['file' => 'Invalid JSON file']);
        }

        $result = $importService->import($data, $request->conflict_strategy);

        if ($result['success']) {
            return redirect()->route('dashboard.suppliers.index')
                ->with('success', 'Import successful! Conflicts handled: ' . count($result['conflicts']));
        } else {
            return back()->withErrors(['file' => $result['message']]);
        }
    }
}