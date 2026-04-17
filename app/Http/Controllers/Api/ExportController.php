<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ExportServiceInterface;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class ExportController extends ApiController
{
    public function __construct(
        protected ExportServiceInterface $exportService,
    ) {
    }

    public function __invoke(Supplier $supplier): JsonResponse
    {
        $supplier = $this->exportService->export($supplier);

        return $this->successResponse(
            'Export generated successfully.',
            (new SupplierResource($supplier))->resolve(),
        );
    }
}
