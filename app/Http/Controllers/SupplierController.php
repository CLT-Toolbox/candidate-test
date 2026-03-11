<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSupplierRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierImportService;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct(
        private SupplierService $supplierService,
        private SupplierImportService $importService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->supplierService->getAll());
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());

        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->load('layups.layers');

        return response()->json($supplier);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->update($supplier, $request->validated());

        return response()->json($supplier);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->supplierService->delete($supplier);

        return response()->json(['message' => 'Supplier deleted successfully.']);
    }

    public function export(Supplier $supplier): JsonResponse
    {
        $data = $this->supplierService->export($supplier);

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="supplier_' . $supplier->id . '_export.json"');
    }

    public function import(ImportSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $fileContent = file_get_contents($request->file('file')->getRealPath());
        $jsonData = json_decode($fileContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['message' => 'Invalid JSON format.'], 422);
        }

        $layupsData = $jsonData['layups'] ?? [];
        $strategy = $request->input('strategy', SupplierImportService::STRATEGY_SKIP);

        $result = $this->importService->importData($supplier, $layupsData, $strategy);

        if ($result['status'] === 'error') {
            return response()->json([
                'message' => $result['message'],
                'conflicts' => $result['conflicts'],
            ], 409);
        }

        return response()->json([
            'message' => $result['message'],
            'statistics' => $result['statistics'],
            'conflicts' => $result['conflicts'],
        ]);
    }
}
