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
