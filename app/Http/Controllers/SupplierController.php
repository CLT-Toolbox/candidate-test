<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Models\Supplier;
use App\Services\CltLayupService;
use App\Services\SupplierService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ApiResponse;

    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    public function index(Request $request)
    {
        $suppliers = $this->supplierService->paginateWithFilters([
            'search' => $request->query('search')
        ]);

        if ($request->wantsJson()) {
            return $this->successResponse($suppliers, 'Suppliers retrieved successfully');
        }

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());
        return $this->successResponse($supplier, 'Supplier created successfully', 201);
    }

    public function show(Request $request, Supplier $supplier, CltLayupService $layupService)
    {
        $layups = $layupService->paginateBySupplier($supplier->id, [
            'search' => $request->query('search')
        ]);

        if ($request->wantsJson()) {
            return $this->successResponse([
                'supplier' => $supplier,
                'layups' => $layups
            ], 'Supplier detail retrieved');
        }

        return view('suppliers.show.index', compact('supplier', 'layups'));
    }

    public function update(SupplierStoreRequest $request, int $id): JsonResponse
    {
        $updated = $this->supplierService->update($id, $request->validated());
        if ($updated) {
            return $this->successResponse(null, 'Supplier updated successfully');
        }
        return $this->errorResponse('Failed to update supplier', 400);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->supplierService->delete($id);
        if ($deleted) {
            return $this->successResponse(null, 'Supplier deleted successfully');
        }
        return $this->errorResponse('Supplier not found or failed to delete', 404);
    }
}
