<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ImportServiceInterface;
use App\Exceptions\ImportConflictException;
use App\Http\Requests\ImportRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class ImportController extends ApiController
{
    public function __construct(
        protected ImportServiceInterface $importService,
    ) {
    }

    public function __invoke(ImportRequest $request, Supplier $supplier): JsonResponse
    {
        try {
            $result = $this->importService->import($supplier, $request->validated());
        } catch (ImportConflictException $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->reportData(),
                422,
            );
        }

        return $this->successResponse('Import completed successfully.', $result);
    }
}
