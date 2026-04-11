<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSupplierRequest;
use App\Models\Supplier;
use App\Services\ImportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    public function __construct(
        private ImportExportService $importExportService
    ) {}

    public function exportList(): StreamedResponse
    {
        $this->authorize('viewAny', Supplier::class);
        $suppliers = Supplier::with('layups.layers')->get();

        $data = [
            'suppliers' => $suppliers->map(function ($supplier) {
                return [
                    'name' => $supplier->name,
                    'layups' => $supplier->layups->map(fn($layup) => [
                        'name' => $layup->name,
                        'layers' => $layup->layers->map(fn($layer) => [
                            'layer_order' => $layer->layer_order,
                            'thickness' => $layer->thickness,
                            'width' => $layer->width,
                            'angle' => $layer->angle,
                        ])->toArray(),
                    ])->toArray(),
                ];
            })->toArray(),
        ];

        $filename = 'suppliers-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function export(Supplier $supplier): StreamedResponse
    {
        $this->authorize('view', $supplier);
        $data = $this->importExportService->export($supplier);
        $filename = 'supplier-' . $supplier->id . '-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function detectConflicts(Supplier $supplier): JsonResponse
    {
        $this->authorize('view', $supplier);
        $content = request()->file('file')->get();
        $importData = json_decode($content, true);

        if (!$importData) {
            return response()->json(['error' => 'Invalid JSON file.'], 422);
        }

        $conflicts = $this->importExportService->detectConflicts($supplier, $importData);

        return response()->json([
            'has_conflicts' => !empty($conflicts),
            'conflicts'     => $conflicts,
        ]);
    }

    public function import(ImportSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $content = $request->file('file')->get();
        $importData = json_decode($content, true);

        if (!$importData) {
            return back()->with('error', 'Invalid JSON file.');
        }

        $strategy = $request->input('strategy');
        $resolutions = $request->input('resolutions', []);

        try {
            $results = $this->importExportService->import($supplier, $importData, $strategy, $resolutions);
            return redirect()->route('suppliers.show', $supplier)
                ->with('success', "Import complete. Created: {$results['created']}, Updated: {$results['updated']}, Skipped: {$results['skipped']}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
