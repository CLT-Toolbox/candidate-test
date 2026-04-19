<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayerStoreRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayerService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CltLayerController extends Controller
{
    use ApiResponse;

    protected $layerService;

    public function __construct(CltLayerService $layerService)
    {
        $this->layerService = $layerService;
        $this->authorizeResource(CltLayer::class, 'layer');
    }

    public function reorder(Request $request, Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $request->validate([
            'layers' => 'required|array',
            'layers.*.id' => 'required|exists:clt_layers,id',
        ]);

        $this->layerService->reorder($request->input('layers'));

        return $this->successResponse(null, 'Layers reordered and saved successfully');
    }

    public function index(Supplier $supplier, CltLayup $layup): JsonResponse
    {
        return $this->successResponse($layup->layers, "Layers for Layup: {$layup->name} retrieved");
    }

    public function store(CltLayerStoreRequest $request, Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $data = $request->validated();
        $data['layup_id'] = $layup->id;

        $layer = $this->layerService->create($data);

        return $this->successResponse($layer, 'Layer added to layup successfully', 201);
    }

    public function show(Supplier $supplier, CltLayup $layup, CltLayer $layer): JsonResponse
    {
        return $this->successResponse($layer, 'Layer detail retrieved');
    }

    public function update(CltLayerStoreRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer): JsonResponse
    {
        $this->layerService->update($layer->id, $request->validated());

        return $this->successResponse($layer->fresh(), 'Layer updated successfully');
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer): JsonResponse
    {
        $this->layerService->delete($layer->id);

        return $this->successResponse(null, 'Layer deleted from layup');
    }
}
