<?php

namespace App\Services;

use App\Exceptions\ImportConflictException;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierImportExportService
{
    /** @return array<string, mixed> */
    public function exportPayload(Supplier $supplier): array
    {
        $supplier->load(['cltLayups.cltLayers']);

        return [
            'supplier' => [
                'name' => $supplier->name,
                'code' => $supplier->code,
                'notes' => $supplier->notes,
            ],
            'layups' => $supplier->cltLayups->map(function (CltLayup $layup) {
                return [
                    'name' => $layup->name,
                    'description' => $layup->description,
                    'layers' => $layup->cltLayers->map(fn (CltLayer $l) => [
                        'layer_order' => $l->layer_order,
                        'thickness' => (float) $l->thickness,
                        'width' => (float) $l->width,
                        'angle' => (float) $l->angle,
                    ])->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload  Must contain 'layups' array
     * @return array{created_layups: int, updated_layers: int, skipped_layers: int, message: string}
     */
    public function import(Supplier $supplier, array $payload, string $strategy = 'overwrite'): array
    {
        $layupsIn = $payload['layups'] ?? null;
        if (! is_array($layupsIn)) {
            throw new \InvalidArgumentException('Invalid import: "layups" must be an array.');
        }

        if ($strategy === 'reject') {
            $conflicts = $this->collectLayerConflicts($supplier, $layupsIn);
            if (count($conflicts) > 0) {
                throw new ImportConflictException($conflicts);
            }
        }

        $createdLayups = 0;
        $updatedLayers = 0;
        $skippedLayers = 0;

        DB::transaction(function () use ($supplier, $layupsIn, $strategy, &$createdLayups, &$updatedLayers, &$skippedLayers) {
            foreach ($layupsIn as $layupData) {
                $name = $layupData['name'] ?? null;
                if (! is_string($name) || $name === '') {
                    continue;
                }

                $existing = CltLayup::where('supplier_id', $supplier->id)->where('name', $name)->first();

                if ($existing && $strategy === 'duplicate') {
                    $newName = $this->uniqueImportedLayupName($supplier, $name);
                    $layup = CltLayup::create([
                        'supplier_id' => $supplier->id,
                        'name' => $newName,
                        'description' => $layupData['description'] ?? null,
                    ]);
                    $createdLayups++;
                    $this->importLayers($layup, $layupData['layers'] ?? [], 'overwrite', $updatedLayers, $skippedLayers);
                } elseif ($existing) {
                    $layup = $existing;
                    if (isset($layupData['description'])) {
                        $layup->update(['description' => $layupData['description']]);
                    }
                    $layerStrategy = in_array($strategy, ['reject', 'duplicate'], true) ? 'overwrite' : $strategy;
                    $this->importLayers($layup, $layupData['layers'] ?? [], $layerStrategy, $updatedLayers, $skippedLayers);
                } else {
                    $layup = CltLayup::create([
                        'supplier_id' => $supplier->id,
                        'name' => $name,
                        'description' => $layupData['description'] ?? null,
                    ]);
                    $createdLayups++;
                    $this->importLayers($layup, $layupData['layers'] ?? [], 'overwrite', $updatedLayers, $skippedLayers);
                }
            }
        });

        return [
            'created_layups' => $createdLayups,
            'updated_layers' => $updatedLayers,
            'skipped_layers' => $skippedLayers,
            'message' => 'Import completed.',
        ];
    }

    private function uniqueImportedLayupName(Supplier $supplier, string $original): string
    {
        $base = $original.' (imported)';
        $name = $base;
        $i = 2;
        while (CltLayup::where('supplier_id', $supplier->id)->where('name', $name)->exists()) {
            $name = $original.' (imported '.$i.')';
            $i++;
        }

        return $name;
    }

    /**
     * @param  array<int, mixed>  $layersIn
     */
    private function importLayers(
        CltLayup $layup,
        array $layersIn,
        string $strategy,
        int &$updatedLayers,
        int &$skippedLayers
    ): void {
        foreach ($layersIn as $row) {
            if (! is_array($row)) {
                continue;
            }
            $order = isset($row['layer_order']) ? (int) $row['layer_order'] : null;
            if ($order === null || $order < 0) {
                continue;
            }

            $thickness = (float) ($row['thickness'] ?? 0);
            $width = (float) ($row['width'] ?? 0);
            $angle = (float) ($row['angle'] ?? 0);

            $existing = CltLayer::where('clt_layup_id', $layup->id)->where('layer_order', $order)->first();

            if (! $existing) {
                CltLayer::create([
                    'clt_layup_id' => $layup->id,
                    'layer_order' => $order,
                    'thickness' => $thickness,
                    'width' => $width,
                    'angle' => $angle,
                ]);
                $updatedLayers++;

                continue;
            }

            $same = $this->floatEq($existing->thickness, $thickness)
                && $this->floatEq($existing->width, $width)
                && $this->floatEq($existing->angle, $angle);

            if ($same) {
                continue;
            }

            if ($strategy === 'overwrite') {
                $existing->update([
                    'thickness' => $thickness,
                    'width' => $width,
                    'angle' => $angle,
                ]);
                $updatedLayers++;
            } elseif ($strategy === 'skip') {
                $skippedLayers++;
            }
        }
    }

    private function floatEq(float|string $a, float $b, float $eps = 0.0001): bool
    {
        return abs((float) $a - $b) < $eps;
    }

    /**
     * Layer-level conflicts: same layer_order, different fields.
     *
     * @param  array<int, mixed>  $layupsIn
     * @return list<array<string, mixed>>
     */
    public function collectLayerConflicts(Supplier $supplier, array $layupsIn): array
    {
        $out = [];

        foreach ($layupsIn as $layupData) {
            $name = $layupData['name'] ?? null;
            if (! is_string($name) || $name === '') {
                continue;
            }

            $layup = CltLayup::where('supplier_id', $supplier->id)->where('name', $name)->first();
            if (! $layup) {
                continue;
            }

            foreach ($layupData['layers'] ?? [] as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $order = isset($row['layer_order']) ? (int) $row['layer_order'] : null;
                if ($order === null) {
                    continue;
                }

                $existing = CltLayer::where('clt_layup_id', $layup->id)->where('layer_order', $order)->first();
                if (! $existing) {
                    continue;
                }

                $thickness = (float) ($row['thickness'] ?? 0);
                $width = (float) ($row['width'] ?? 0);
                $angle = (float) ($row['angle'] ?? 0);

                if (
                    $this->floatEq($existing->thickness, $thickness)
                    && $this->floatEq($existing->width, $width)
                    && $this->floatEq($existing->angle, $angle)
                ) {
                    continue;
                }

                $out[] = [
                    'layup' => $name,
                    'layer_order' => $order,
                    'existing' => [
                        'thickness' => (float) $existing->thickness,
                        'width' => (float) $existing->width,
                        'angle' => (float) $existing->angle,
                    ],
                    'incoming' => [
                        'thickness' => $thickness,
                        'width' => $width,
                        'angle' => $angle,
                    ],
                ];
            }
        }

        return $out;
    }
}
