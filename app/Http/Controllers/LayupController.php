<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportLayupsRequest;
use App\Http\Requests\StoreLayupRequest;
use App\Http\Requests\SyncLayupLayersRequest;
use App\Http\Requests\UpdateLayupRequest;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Services\LayupFileParserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LayupController extends Controller
{
    public function __construct(
        private readonly LayupFileParserService $layupFileParser
    ) {}

    public function index(Supplier $supplier): View
    {
        $layups = $supplier->layups()->with('layers')->withCount('layers')->get();

        return view('layups.index', compact('supplier', 'layups'));
    }

    public function create(Supplier $supplier): View
    {
        return view('layups.create', compact('supplier'));
    }

    public function store(StoreLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validated();
        $validated['supplier_id'] = $supplier->supplier_id;
        $validated['layup_id'] = Layup::generateLayupId();
        Layup::create($validated);

        return redirect()->route('suppliers.show', $supplier->supplier_id)->with('success', 'Layup created successfully.');
    }

    public function show(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('view', $layup);
        $layup->load(['layers' => fn ($q) => $q->orderBy('layer_order')]);

        return view('layups.show', compact('supplier', 'layup'));
    }

    public function syncLayers(SyncLayupLayersRequest $request, Supplier $supplier, Layup $layup)
    {
        $this->authorize('update', $layup);

        if ($layup->supplier_id !== $supplier->supplier_id) {
            abort(404);
        }

        $validated = $request->validated();

        DB::transaction(function () use ($layup, $validated) {
            Layer::where('layup_id', $layup->layup_id)->delete();

            foreach ($validated['layers'] as $i => $row) {
                Layer::create([
                    'layup_id' => $layup->layup_id,
                    'layer_order' => $i + 1,
                    'thickness' => $row['thickness'],
                    'width' => $row['width'],
                    'angle' => $row['angle'],
                    'grade' => $row['grade'] ?? null,
                ]);
            }

            $sum = collect($validated['layers'])->sum(fn ($r) => (float) $r['thickness']);
            $layup->update(['thickness' => (string) round($sum, 2)]);
        });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => __('Layup layers saved.')]);
        }

        return redirect()
            ->route('suppliers.layups.show', [$supplier->supplier_id, $layup->layup_id])
            ->with('success', __('Layup layers saved.'));
    }

    public function duplicate(Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('update', $layup);

        if ($layup->supplier_id !== $supplier->supplier_id) {
            abort(404);
        }

        $layup->load(['layers' => fn ($q) => $q->orderBy('layer_order')]);

        $newLayup = DB::transaction(function () use ($supplier, $layup) {
            $copy = Layup::create([
                'supplier_id' => $supplier->supplier_id,
                'layup_id' => Layup::generateLayupId(),
                'name' => $layup->name.' (copy)',
                'description' => $layup->description,
                'thickness' => $layup->thickness,
                'grade' => $layup->grade,
                'status' => Layup::STATUS_INACTIVE,
            ]);

            foreach ($layup->layers as $layer) {
                Layer::create([
                    'layup_id' => $copy->layup_id,
                    'layer_order' => $layer->layer_order,
                    'thickness' => $layer->thickness,
                    'width' => $layer->width,
                    'angle' => $layer->angle,
                    'grade' => $layer->grade,
                ]);
            }

            return $copy;
        });

        return redirect()
            ->route('suppliers.layups.show', [$supplier->supplier_id, $newLayup->layup_id])
            ->with('success', __('Layup duplicated.'));
    }

    public function edit(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('update', $layup);

        return view('layups.edit', compact('supplier', 'layup'));
    }

    public function update(UpdateLayupRequest $request, Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('update', $layup);

        $layup->update($request->validated());

        return redirect()->route('suppliers.show', $supplier->supplier_id)->with('success', 'Layup updated successfully.');
    }

    public function destroy(Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('delete', $layup);
        $layup->delete();

        return redirect()->route('suppliers.layups.index', $supplier)->with('success', 'Layup deleted successfully.');
    }

    public function import(ImportLayupsRequest $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validated();
        $file = $request->file('file');
        $conflictStrategy = $request->input('conflict_strategy', 'skip');
        $isDryRun = $request->boolean('dry_run');
        $resolutions = [];

        if ($request->filled('conflict_resolutions')) {
            $resolutions = json_decode($request->input('conflict_resolutions'), true) ?? [];
        }

        $layups = $this->layupFileParser->parseForLayupImport($file);

        $importedCount = 0;
        $skippedCount = 0;
        $duplicatedCount = 0;
        $conflictDetails = [];

        if ($conflictStrategy === 'reject') {
            foreach ($layups as $data) {
                $existingLayup = Layup::where('supplier_id', $supplier->supplier_id)
                    ->where('name', $data['name'] ?? null)
                    ->first();

                if ($existingLayup) {
                    $conflictDetails[] = [
                        'name' => $data['name'] ?? 'Unknown',
                        'existing_id' => $existingLayup->layup_id,
                        'incoming_data' => $data,
                    ];
                }
            }

            if (! empty($conflictDetails)) {
                $message = 'Import rejected due to '.count($conflictDetails).' conflict(s) detected. ';
                $message .= implode(', ', array_map(fn ($c) => $c['name'], $conflictDetails)).' already exist in the database.';

                return redirect()->route('suppliers.show', $supplier->supplier_id)
                    ->with('error', $message);
            }
        }

        foreach ($layups as $index => $data) {
            $layers = $data['layers'] ?? [];
            unset($data['layers']);

            $data['supplier_id'] = $supplier->supplier_id;

            $existingLayup = Layup::where('supplier_id', $supplier->supplier_id)
                ->where('name', $data['name'] ?? null)
                ->first();

            if ($existingLayup) {
                $userResolution = $resolutions[$index] ?? null;

                if ($userResolution === 'keep') {
                    $skippedCount++;
                    continue;
                }
                if ($userResolution === 'accept') {
                    $conflictStrategy = 'overwrite';
                } elseif ($conflictStrategy === 'skip') {
                    $skippedCount++;
                    continue;
                } elseif ($conflictStrategy === 'duplicate') {
                    $data['name'] = ($data['name'] ?? 'Untitled').' (imported)';
                    $data['layup_id'] = Layup::generateLayupId();

                    if (! $isDryRun) {
                        $newLayup = Layup::create($data);
                        if (! empty($layers)) {
                            foreach ($layers as $layer) {
                                $layer['layup_id'] = $newLayup->layup_id;
                                Layer::create($layer);
                            }
                        }
                    }
                    $duplicatedCount++;
                    continue;
                }
            }

            if (! $isDryRun) {
                if ($existingLayup && $conflictStrategy === 'overwrite') {
                    $existingLayup->update($data);
                    Layer::where('layup_id', $existingLayup->layup_id)->delete();
                    if (! empty($layers)) {
                        foreach ($layers as $layer) {
                            $layer['layup_id'] = $existingLayup->layup_id;
                            Layer::create($layer);
                        }
                    }
                } else {
                    if (! $existingLayup) {
                        $data['layup_id'] = Layup::generateLayupId();
                    }
                    $newLayup = Layup::create($data);
                    if (! empty($layers)) {
                        foreach ($layers as $layer) {
                            $layer['layup_id'] = $newLayup->layup_id;
                            Layer::create($layer);
                        }
                    }
                }
            }

            $importedCount++;
        }

        $message = "Import completed. {$importedCount} layup(s) imported.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} conflict(s) skipped.";
        }
        if ($duplicatedCount > 0) {
            $message .= " {$duplicatedCount} layup(s) created with suffix.";
        }
        if ($isDryRun) {
            $message .= ' (Dry run - no data saved)';
        }

        return redirect()->route('suppliers.show', $supplier->supplier_id)->with('success', $message);
    }
}
