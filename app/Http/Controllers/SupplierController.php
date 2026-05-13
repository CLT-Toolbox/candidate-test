<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->get();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'suppliers_export.csv';

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () {

            $file = fopen('php://output', 'w');

            // header CSV
            fputcsv($file, [
                'supplier',
                'layup',
                'layer_order',
                'thickness',
                'width',
                'angle'
            ]);

            $suppliers = Supplier::with('layups.layers')->get();

            foreach ($suppliers as $supplier) {
                foreach ($supplier->layups as $layup) {
                    foreach ($layup->layers as $layer) {

                        fputcsv($file, [
                            $supplier->name,
                            $layup->name,
                            $layer->layer_order,
                            $layer->thickness,
                            $layer->width,
                            $layer->angle,
                        ]);

                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Supplier::create([
            'name' => $request->name,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $supplier->update([
            'name' => $request->name,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully');
    }
}
