<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\Layup;
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

    public function importForm()
    {
        return view('suppliers.import');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'strategy' => 'required|in:overwrite,skip,duplicate,reject',
        ]);

        $strategy = $request->strategy;

        $file = fopen($request->file('file'), 'r');

        fgetcsv($file); // skip header

        // PROCESS ROWS
        while (($row = fgetcsv($file)) !== false) {

            [$supplierName, $layupName, $order, $thickness, $width, $angle] = $row;

            $supplier = Supplier::firstOrCreate(['name' => $supplierName]);

            $layup = Layup::firstOrCreate([
                'supplier_id' => $supplier->id,
                'name' => $layupName
            ]);

            $existingLayer = Layer::where('layup_id', $layup->id)
                ->where('layer_order', $order)
                ->first();

            if ($existingLayer) {

                $isConflict =
                    $existingLayer->thickness != $thickness ||
                    $existingLayer->width != $width ||
                    $existingLayer->angle != $angle;

                if ($isConflict && $strategy === 'reject') {
                    fclose($file);
                    return back()->with('error', 'Import aborted due to conflict.');
                }

                if ($isConflict && $strategy === 'skip') {
                    continue;
                }

                if ($isConflict && $strategy === 'overwrite') {
                    $existingLayer->update([
                        'thickness' => $thickness,
                        'width' => $width,
                        'angle' => $angle,
                    ]);
                    continue;
                }

                if ($isConflict && $strategy === 'duplicate') {
                    Layer::create([
                        'layup_id' => $layup->id,
                        'layer_order' => $order,
                        'thickness' => $thickness,
                        'width' => $width,
                        'angle' => $angle,
                    ]);
                    continue;
                }
            }

            Layer::create([
                'layup_id' => $layup->id,
                'layer_order' => $order,
                'thickness' => $thickness,
                'width' => $width,
                'angle' => $angle,
            ]);
        }

        fclose($file);

        return redirect()->route('suppliers.index')
            ->with('success', 'Import CSV completed successfully');
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
