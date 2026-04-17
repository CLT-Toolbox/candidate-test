<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\LayerServiceInterface;
use App\Http\Requests\StoreLayerRequest;
use App\Http\Requests\UpdateLayerRequest;
use App\Http\Resources\LayerResource;
use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LayerController extends ApiController
{
    public function __construct(
        protected LayerServiceInterface $layerService,
    ) {
    }

    public function index(Request $request, Layup $layup): JsonResponse
    {
        $layers = $this->layerService->index($layup, $request->only(['layer_order', 'per_page']));

        return $this->successResponse(
            'Layers retrieved successfully.',
            LayerResource::collection($layers)->response()->getData(true),
        );
    }

    public function store(StoreLayerRequest $request, Layup $layup): JsonResponse
    {
        $layer = $this->layerService->store($layup, $request->validated());

        return $this->successResponse(
            'Layer created successfully.',
            (new LayerResource($layer))->resolve(),
            201,
        );
    }

    public function show(Layer $layer): JsonResponse
    {
        $layer = $this->layerService->show($layer);

        return $this->successResponse(
            'Layer retrieved successfully.',
            (new LayerResource($layer))->resolve(),
        );
    }

    public function update(UpdateLayerRequest $request, Layer $layer): JsonResponse
    {
        $layer = $this->layerService->update($layer, $request->validated());

        return $this->successResponse(
            'Layer updated successfully.',
            (new LayerResource($layer))->resolve(),
        );
    }

    public function destroy(Layer $layer): JsonResponse
    {
        $this->layerService->destroy($layer);

        return $this->successResponse('Layer deleted successfully.', null);
    }
}
