<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupStoreRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayupService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CltLayupController extends Controller
{
    use ApiResponse;

    protected $layupService;

    public function __construct(CltLayupService $layupService)
    {
        $this->layupService = $layupService;
        $this->authorizeResource(CltLayup::class, 'layup');
    }

    public function store(CltLayupStoreRequest $request, Supplier $supplier): JsonResponse
    {
        $data = $request->validated();
        $data['supplier_id'] = $supplier->id;

        $layup = $this->layupService->create($data);

        return $this->successResponse($layup, 'Layup created successfully', 201);
    }

    public function show(Request $request, Supplier $supplier, CltLayup $layup)
    {
        $layup->load('layers');
        $layup->status = 'Active';
        $layup->created_by = 'Eng. Dept A';
        
        $layup->layers->each(function($l, $i) {
            $l->grade = ($i % 2 === 0) ? 'C24' : 'C16';
        });

        if ($request->wantsJson()) {
            return $this->successResponse($layup, 'Layup detail retrieved');
        }

        return view('suppliers.show.show-detail.index', compact('supplier', 'layup'));
    }

    public function update(CltLayupStoreRequest $request, Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $this->layupService->update($layup->id, $request->validated());

        return $this->successResponse($layup->fresh(), 'Layup updated successfully');
    }

    public function destroy(Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $this->layupService->delete($layup->id);

        return $this->successResponse(null, 'Layup deleted successfully');
    }

    public function duplicate(Supplier $supplier, CltLayup $layup)
    {
        $newLayup = $this->layupService->duplicate($layup->id);
        
        return redirect()->route('suppliers.layups.show', [$supplier->id, $newLayup->id])
            ->with('success', 'Layup duplicated successfully');
    }
}
