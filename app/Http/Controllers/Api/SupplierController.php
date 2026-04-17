<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\SupplierServiceInterface;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends ApiController
{
    public function __construct(
        protected SupplierServiceInterface $supplierService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $suppliers = $this->supplierService->index($request->only(['name', 'per_page']));

        return $this->successResponse(
            'Suppliers retrieved successfully.',
            SupplierResource::collection($suppliers)->response()->getData(true),
        );
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->store($request->validated());

        return $this->successResponse(
            'Supplier created successfully.',
            (new SupplierResource($supplier))->resolve(),
            201,
        );
    }

    public function show(Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->show($supplier);

        return $this->successResponse(
            'Supplier retrieved successfully.',
            (new SupplierResource($supplier))->resolve(),
        );
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->update($supplier, $request->validated());

        return $this->successResponse(
            'Supplier updated successfully.',
            (new SupplierResource($supplier))->resolve(),
        );
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->supplierService->destroy($supplier);

        return $this->successResponse('Supplier deleted successfully.', null);
    }
}
