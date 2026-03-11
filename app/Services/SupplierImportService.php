<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\Layup;
use App\Models\Layer;
use Illuminate\Support\Facades\DB;
use Exception;

class SupplierImportService
{
    const STRATEGY_SKIP = 'skip';
    const STRATEGY_OVERWRITE = 'overwrite';
    const STRATEGY_DUPLICATE = 'duplicate';
    const STRATEGY_REJECT = 'reject';

    public function importData(Supplier $supplier, array $layupsData, string $strategy = self::STRATEGY_SKIP): array
    {
        DB::beginTransaction();

        try {
            $conflicts = [];
            $stats = [
                'layups_created' => 0,
                'layups_updated' => 0,
                'layers_created' => 0,
                'layers_updated' => 0,
                'conflicts_found' => 0,
            ];

            foreach ($layupsData as $layupData) {
                $layup = Layup::firstOrCreate([
                    'supplier_id' => $supplier->id,
                    'name' => $layupData['name'],
                ]);

                $layupIsNew = $layup->wasRecentlyCreated;

                if (!$layupIsNew && $strategy === self::STRATEGY_DUPLICATE) {
                    $layup = $this->duplicateLayup($supplier, $layupData['name']);
                    $stats['layups_created']++;

                    $conflicts[] = [
                        'type' => 'layup_duplicated',
                        'original_name' => $layupData['name'],
                        'new_name' => $layup->name,
                        'message' => "Layup '{$layupData['name']}' duplicated as '{$layup->name}'",
                    ];
                }

                if (isset($layupData['layers'])) {
                    $this->processLayers(
                        $layup,
                        $layupData['layers'],
                        $strategy,
                        $conflicts,
                        $stats
                    );
                }

                if ($layupIsNew) {
                    $stats['layups_created']++;
                }
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => $this->buildSummary($stats),
                'statistics' => $stats,
                'conflicts' => $conflicts,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'statistics' => $stats ?? [],
                'conflicts' => $conflicts ?? [],
            ];
        }
    }

    private function duplicateLayup(Supplier $supplier, string $originalName): Layup
    {
        $newName = $originalName . ' (imported)';
        $counter = 2;

        while (Layup::where('supplier_id', $supplier->id)->where('name', $newName)->exists()) {
            $newName = $originalName . ' (imported ' . $counter . ')';
            $counter++;
        }

        return Layup::create([
            'supplier_id' => $supplier->id,
            'name' => $newName,
        ]);
    }

    private function processLayers(
        Layup $layup,
        array $layersData,
        string $strategy,
        array &$conflicts,
        array &$stats
    ): void {
        foreach ($layersData as $layerData) {
            $existing = Layer::where('layup_id', $layup->id)
                ->where('layer_order', $layerData['layer_order'])
                ->first();

            if (!$existing) {
                Layer::create([
                    'layup_id' => $layup->id,
                    'layer_order' => $layerData['layer_order'],
                    'thickness' => $layerData['thickness'],
                    'width' => $layerData['width'],
                    'angle' => $layerData['angle'],
                ]);
                $stats['layers_created']++;
                continue;
            }

            $hasDifference = $this->detectDifference($existing, $layerData);

            if (!$hasDifference) {
                continue;
            }

            $stats['conflicts_found']++;

            $conflictDetail = [
                'type' => 'layer_conflict',
                'layup_name' => $layup->name,
                'layer_order' => $layerData['layer_order'],
                'existing_data' => [
                    'thickness' => $existing->thickness,
                    'width' => $existing->width,
                    'angle' => $existing->angle,
                ],
                'import_data' => [
                    'thickness' => $layerData['thickness'],
                    'width' => $layerData['width'],
                    'angle' => $layerData['angle'],
                ],
            ];

            $this->resolveConflict($existing, $layerData, $strategy, $conflictDetail, $conflicts, $stats);
        }
    }

    private function detectDifference(Layer $existing, array $incoming): bool
    {
        return (string) $existing->thickness !== (string) $incoming['thickness']
            || (string) $existing->width !== (string) $incoming['width']
            || (string) $existing->angle !== (string) $incoming['angle'];
    }

    private function resolveConflict(
        Layer $existing,
        array $incoming,
        string $strategy,
        array $conflictDetail,
        array &$conflicts,
        array &$stats
    ): void {
        switch ($strategy) {
            case self::STRATEGY_SKIP:
                $conflictDetail['resolution'] = 'skipped';
                $conflictDetail['message'] = "Layer #{$incoming['layer_order']} skipped (kept existing data)";
                $conflicts[] = $conflictDetail;
                break;

            case self::STRATEGY_OVERWRITE:
                $existing->update([
                    'thickness' => $incoming['thickness'],
                    'width' => $incoming['width'],
                    'angle' => $incoming['angle'],
                ]);
                $conflictDetail['resolution'] = 'overwritten';
                $conflictDetail['message'] = "Layer #{$incoming['layer_order']} overwritten with import data";
                $conflicts[] = $conflictDetail;
                $stats['layers_updated']++;
                break;

            case self::STRATEGY_REJECT:
                $conflictDetail['resolution'] = 'rejected';
                $conflicts[] = $conflictDetail;
                throw new Exception(
                    "Conflict detected: Layer #{$incoming['layer_order']} in layup '{$existing->layup->name}' " .
                    "has different values. Import rejected."
                );
        }
    }

    private function buildSummary(array $stats): string
    {
        $parts = [];

        if ($stats['layups_created'] > 0) {
            $parts[] = "{$stats['layups_created']} layup(s) created";
        }

        if ($stats['layers_created'] > 0) {
            $parts[] = "{$stats['layers_created']} layer(s) created";
        }

        if ($stats['layers_updated'] > 0) {
            $parts[] = "{$stats['layers_updated']} layer(s) updated";
        }

        if ($stats['conflicts_found'] > 0) {
            $parts[] = "{$stats['conflicts_found']} conflict(s) resolved";
        }

        $summary = 'Import completed successfully.';

        if (!empty($parts)) {
            $summary .= ' ' . implode(', ', $parts) . '.';
        }

        return $summary;
    }
}
