<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\LayupServiceInterface;
use App\Http\Requests\StoreLayupRequest;
use App\Http\Requests\UpdateLayupRequest;
use App\Http\Resources\LayupResource;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LayupController extends ApiController
{
    public function __construct(
        protected LayupServiceInterface $layupService,
    ) {
    }

    public function index(Request $request, Supplier $supplier): JsonResponse
    {
        $layups = $this->layupService->index($supplier, $request->only(['name', 'per_page']));

        return $this->successResponse(
            'Layups retrieved successfully.',
            LayupResource::collection($layups)->response()->getData(true),
        );
    }

    public function store(StoreLayupRequest $request, Supplier $supplier): JsonResponse
    {
        $layup = $this->layupService->store($supplier, $request->validated());

        return $this->successResponse(
            'Layup created successfully.',
            (new LayupResource($layup))->resolve(),
            201,
        );
    }

    public function show(Layup $layup): JsonResponse
    {
        $layup = $this->layupService->show($layup);

        return $this->successResponse(
            'Layup retrieved successfully.',
            (new LayupResource($layup))->resolve(),
        );
    }

    public function update(UpdateLayupRequest $request, Layup $layup): JsonResponse
    {
        $layup = $this->layupService->update($layup, $request->validated());

        return $this->successResponse(
            'Layup updated successfully.',
            (new LayupResource($layup))->resolve(),
        );
    }

    public function destroy(Layup $layup): JsonResponse
    {
        $this->layupService->destroy($layup);

        return $this->successResponse('Layup deleted successfully.', null);
    }
}
