<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayupRequest;
use App\Http\Requests\UpdateLayupRequest;
use App\Models\Layup;
use App\Services\LayupService;
use Illuminate\Http\JsonResponse;

class LayupController extends Controller
{
    public function __construct(
        private LayupService $layupService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->layupService->getAll());
    }

    public function store(StoreLayupRequest $request): JsonResponse
    {
        $layup = $this->layupService->create($request->validated());

        return response()->json($layup, 201);
    }

    public function show(Layup $layup): JsonResponse
    {
        $layup->load('layers');

        return response()->json($layup);
    }

    public function update(UpdateLayupRequest $request, Layup $layup): JsonResponse
    {
        $layup = $this->layupService->update($layup, $request->validated());

        return response()->json($layup);
    }

    public function destroy(Layup $layup): JsonResponse
    {
        $this->layupService->delete($layup);

        return response()->json(['message' => 'Layup deleted successfully.']);
    }
}
