<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\ExportService;
use App\Services\ImportService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    use ApiResponse;

    protected $importService;
    protected $exportService;

    public function __construct(ImportService $importService, ExportService $exportService)
    {
        $this->importService = $importService;
        $this->exportService = $exportService;
    }

    public function export(Supplier $supplier): StreamedResponse
    {
        $this->authorize('export', [\App\Models\CltLayup::class, $supplier]);

        return response()->streamDownload(function () use ($supplier) {
            $data = $this->exportService->exportToResponse($supplier);
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, "export-{$supplier->name}.json", [
            'Content-Type' => 'application/json',
        ]);
    }

    public function importAnalyze(Request $request, Supplier $supplier): JsonResponse
    {
        $this->authorize('import', [\App\Models\CltLayup::class, $supplier]);

        $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        $json = file_get_contents($request->file('file')->path());
        $data = json_decode($json, true);

        if (!$data) {
            return $this->errorResponse('Invalid JSON format', 400);
        }

        $analysis = $this->importService->analyze($supplier, $data);

        return $this->successResponse([
            'analysis' => $analysis,
            'raw_data' => $data,
        ], 'Import analysis complete');
    }

    public function importConfirm(Request $request, Supplier $supplier): JsonResponse
    {
        $this->authorize('import', [\App\Models\CltLayup::class, $supplier]);

        $request->validate([
            'data' => 'required|array',
            'resolutions' => 'nullable|array',
            'strategy' => 'required|string|in:skip,overwrite,duplicate',
            'dry_run' => 'nullable|boolean'
        ]);

        $this->importService->execute(
            $supplier, 
            $request->input('data'), 
            $request->input('resolutions', []), 
            $request->input('strategy'),
            (bool) $request->input('dry_run', false)
        );

        return $this->successResponse(null, 'Data imported successfully');
    }
}
