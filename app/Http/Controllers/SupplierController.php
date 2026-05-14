<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckSupplierLayupConflictsRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Layup;
use App\Models\Supplier;
use App\Services\LayupFileParserService;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct(
        private readonly LayupFileParserService $layupFileParser
    ) {}

    /**
     * Display a listing of suppliers.
     */
    public function index()
    {
        $suppliers = Supplier::query()
            ->withCount('layups')
            ->orderBy('name')
            ->get();

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(StoreSupplierRequest $request)
    {
        $validated = $request->validated();
        $validated['supplier_id'] = Supplier::generateSupplierId();

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('layups.layers');

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->back()->with('success', __('Supplier updated successfully.'));
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Check for conflicts in uploaded layup file.
     */
    public function checkConflicts(CheckSupplierLayupConflictsRequest $request, Supplier $supplier): JsonResponse
    {
        $file = $request->file('file');
        $layups = $this->layupFileParser->parseForConflictCheck($file);

        $conflictCount = 0;
        $conflicts = [];

        foreach ($layups as $data) {
            $existingLayup = Layup::where('supplier_id', $supplier->supplier_id)
                ->where('name', $data['name'] ?? null)
                ->first();

            if ($existingLayup) {
                $conflictCount++;
                $layers = $data['layers'] ?? [];
                $existingLayers = $existingLayup->layers ?? [];

                $conflicts[] = [
                    'incoming_name' => $data['name'] ?? 'Unknown',
                    'existing_id' => $existingLayup->layup_id,
                    'existing_data' => [
                        'layup_id' => $existingLayup->layup_id,
                        'name' => $existingLayup->name,
                        'description' => $existingLayup->description,
                        'thickness' => $existingLayup->thickness,
                        'grade' => $existingLayup->grade,
                        'status' => $existingLayup->status,
                        'updated_at' => $existingLayup->updated_at?->format('M d, Y'),
                        'layers' => $existingLayers->map(fn ($layer) => [
                            'layer_order' => $layer->layer_order,
                            'thickness' => $layer->thickness,
                            'width' => $layer->width,
                            'angle' => $layer->angle,
                        ])->toArray(),
                    ],
                    'incoming_data' => [
                        'name' => $data['name'] ?? '',
                        'description' => $data['description'] ?? '',
                        'thickness' => $data['thickness'] ?? '',
                        'grade' => $data['grade'] ?? '',
                        'status' => $data['status'] ?? 'ACTIVE',
                        'layers' => $layers,
                    ],
                ];
            }
        }

        return response()->json([
            'conflict_count' => $conflictCount,
            'total_layups' => count($layups),
            'conflicts' => $conflicts,
        ]);
    }

    /**
     * Get layers for a specific layup
     */
    public function getLayupLayers(Supplier $supplier, string $layupId): JsonResponse
    {
        $layup = Layup::where('supplier_id', $supplier->supplier_id)
            ->where('layup_id', $layupId)
            ->with('layers')
            ->firstOrFail();

        return response()->json([
            'layup_id' => $layup->layup_id,
            'name' => $layup->name,
            'layers' => $layup->layers->sortBy('layer_order')->map(fn ($layer) => [
                'id' => $layer->id,
                'layer_order' => $layer->layer_order,
                'thickness' => $layer->thickness,
                'width' => $layer->width,
                'angle' => $layer->angle,
                'grade' => $layer->grade,
            ])->values()->toArray(),
        ]);
    }
}
