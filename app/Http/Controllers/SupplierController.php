<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private ExportService $exportService;
    private ImportService $importService;

    public function __construct()
    {
        $this->exportService = new ExportService();
        $this->importService = new ImportService();
    }

    public function index()
    {
        $suppliers = Supplier::with('layups.layers')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:suppliers',
        ]);

        $supplier = Supplier::create($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Supplier created successfully.');
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
            'name' => 'required|string|unique:suppliers,name,' . $supplier->id,
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function export(Supplier $supplier)
    {
        $data = $this->exportService->export($supplier);
        return response()->json($data);
    }

    public function importForm()
    {
        return view('suppliers.import');
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:json',
            ]);

            $file = $request->file('file');
            $data = json_decode(file_get_contents($file->getRealPath()), true);

            if (!isset($data['name'])) {
                return back()->with('error', 'Invalid JSON structure. Missing "name" field.');
            }

            // Use default strategy (skip conflicts)
            $this->importService->setConflictStrategy(ImportService::STRATEGY_SKIP);
            
            $result = $this->importService->import($data);
            $conflicts = $this->importService->getConflicts();

            $supplier = $result['supplier'];

            if (!empty($conflicts)) {
                session()->flash('warning', 'Data imported with ' . count($conflicts) . ' conflicts detected. Check the import details.');
            } else {
                session()->flash('success', 'Data imported successfully.');
            }

            return redirect()->route('suppliers.show', $supplier);
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}

