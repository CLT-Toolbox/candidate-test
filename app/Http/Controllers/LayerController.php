<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayerRequest;
use App\Http\Requests\UpdateLayerRequest;
use App\Models\Layer;
use App\Services\LayerService;
use Illuminate\Http\JsonResponse;

class LayerController extends Controller
{
    public function __construct(
        private LayerService $layerService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->layerService->getAll());
    }

    public function store(StoreLayerRequest $request): JsonResponse
    {
        try {
            $layer = $this->layerService->create($request->validated());

            return response()->json($layer, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Layer $layer): JsonResponse
    {
        $layer->load('layup');

        return response()->json($layer);
    }

    public function update(UpdateLayerRequest $request, Layer $layer): JsonResponse
    {
        try {
            $layer = $this->layerService->update($layer, $request->validated());

            return response()->json($layer);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Layer $layer): JsonResponse
    {
        $this->layerService->delete($layer);

        return response()->json(['message' => 'Layer deleted successfully.']);
    }
}
