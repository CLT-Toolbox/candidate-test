<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSupplierRequest;
use App\Models\Supplier;
use App\Services\ImportExportService;
use Barryvdh\DomPDF\Facade\Pdf;
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
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function exportListCsv(): StreamedResponse
    {
        $this->authorize('viewAny', Supplier::class);
        $suppliers = Supplier::with('layups.layers')->get();

        $filename = 'suppliers-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($suppliers) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Supplier Name',
                'Layup Name',
                'Layer Order',
                'Thickness',
                'Width',
                'Angle'
            ]);

            foreach ($suppliers as $supplier) {
                $hasLayups = $supplier->layups->count() > 0;

                if (!$hasLayups) {
                    fputcsv($handle, [
                        $supplier->name,
                        '',
                        '',
                        '',
                        '',
                        ''
                    ]);
                } else {
                    $isFirstSupplier = true;
                    foreach ($supplier->layups as $layup) {
                        $hasLayers = $layup->layers->count() > 0;

                        if (!$hasLayers) {
                            fputcsv($handle, [
                                $isFirstSupplier ? $supplier->name : '',
                                $layup->name,
                                '',
                                '',
                                '',
                                ''
                            ]);
                            $isFirstSupplier = false;
                        } else {
                            $isFirstLayer = true;
                            foreach ($layup->layers as $layer) {
                                fputcsv($handle, [
                                    $isFirstSupplier ? $supplier->name : '',
                                    $isFirstLayer ? $layup->name : '',
                                    $layer->layer_order ?? '',
                                    $layer->thickness ?? '',
                                    $layer->width ?? '',
                                    $layer->angle ?? ''
                                ]);
                                $isFirstSupplier = false;
                                $isFirstLayer = false;
                            }
                        }
                    }
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function exportListPdf(): \Illuminate\Http\Response
    {
        $this->authorize('viewAny', Supplier::class);
        $suppliers = Supplier::with('layups.layers')->get();

        $html = view('export.suppliers-pdf', compact('suppliers'))->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->setOption('defaultFont', 'Courier')
            ->setOption('margin_left', 10)
            ->setOption('margin_right', 10)
            ->setOption('margin_top', 10)
            ->setOption('margin_bottom', 10);

        return $pdf->download('suppliers-' . now()->format('Ymd-His') . '.pdf');
    }

    public function export(Supplier $supplier): StreamedResponse
    {
        $this->authorize('view', $supplier);
        $data = $this->importExportService->export($supplier);
        $filename = 'supplier-' . $supplier->id . '-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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

    public function import(ImportSupplierRequest $request, Supplier $supplier): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $supplier);

        $content = $request->file('file')->get();
        $importData = json_decode($content, true);

        if (!$importData) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid JSON file.'
            ], 422);
        }

        $strategy = $request->input('strategy');
        $resolutionsInput = $request->input('resolutions', '{}');
        $resolutions = is_string($resolutionsInput)
            ? json_decode($resolutionsInput, true) ?? []
            : $resolutionsInput;

        $dry_run = $request->input('dry_run') === '1';

        try {
            $results = $this->importExportService->import(
                $supplier,
                $importData,
                $strategy,
                $resolutions,
                $dry_run
            );

            return response()->json([
                'success' => true,
                'message' => "Import complete. Created: {$results['created']}, Updated: {$results['updated']}, Skipped: {$results['skipped']}.",
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'DRY_RUN_COMPLETED') {
                return response()->json([
                    'success' => true,
                    'message' => 'Dry run completed - no changes saved to database.',
                    'dry_run' => true,
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
