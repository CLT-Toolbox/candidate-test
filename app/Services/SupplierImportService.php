<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\CLT_Layup;
use Illuminate\Support\Facades\DB;

class SupplierImportService
{
    protected ConflictDetector $conflictDetector;

    public function __construct(ConflictDetector $conflictDetector)
    {
        $this->conflictDetector = $conflictDetector;
    }

    public function import(array $importData, Supplier $supplier, array $resolutionMap = []): array
    {
        $conflicts = $this->conflictDetector->detectConflicts($importData, $supplier);

        if (!empty($conflicts) && empty($resolutionMap)) {
            return [
                'success' => false,
                'conflicts' => $conflicts,
                'message' => 'Conflicts detected. Please resolve them before importing.',
            ];
        }

        return DB::transaction(function () use ($importData, $supplier, $conflicts, $resolutionMap) {
            $importedLayups = [];
            $skippedLayups = [];

            foreach ($importData['layups'] as $layupIndex => $incomingLayup) {
                $existingLayup = $supplier->layups()
                    ->where('name', $incomingLayup['name'])
                    ->first();

                $layupResolution = $resolutionMap[$layupIndex] ?? null;

                if ($existingLayup && $layupResolution) {
                    $result = $this->resolveLayupConflict(
                        $existingLayup,
                        $incomingLayup,
                        $layupResolution
                    );

                    if ($result['imported']) {
                        $importedLayups[] = $result;
                    } else {
                        $skippedLayups[] = $result;
                    }
                } elseif (!$existingLayup) {
                    $newLayup = $supplier->layups()->create([
                        'name' => $incomingLayup['name'],
                    ]);

                    foreach ($incomingLayup['layers'] as $layer) {
                        $newLayup->layers()->create([
                            'layer_order' => $layer['layer_order'],
                            'thickness' => $layer['thickness'],
                            'width' => $layer['width'],
                            'angle' => $layer['angle'],
                        ]);
                    }

                    $importedLayups[] = [
                        'type' => 'created',
                        'layup_name' => $newLayup->name,
                        'layup_id' => $newLayup->id,
                        'layer_count' => count($incomingLayup['layers']),
                    ];
                }
            }

            return [
                'success' => true,
                'imported_layups' => $importedLayups,
                'skipped_layups' => $skippedLayups,
                'message' => 'Import completed successfully.',
            ];
        });
    }

    private function resolveLayupConflict(CLT_Layup $existingLayup, array $incomingLayup, array $resolution): array
    {
        $strategy = $resolution['strategy'] ?? 'skip';

        switch ($strategy) {
            case 'overwrite':
                return $this->overwriteLayup($existingLayup, $incomingLayup);

            case 'duplicate':
                return $this->duplicateLayup($existingLayup, $incomingLayup);

            case 'skip':
            default:
                return [
                    'imported' => false,
                    'type' => 'skipped',
                    'layup_name' => $existingLayup->name,
                    'reason' => 'User chose to skip conflict.',
                ];
        }
    }

    private function overwriteLayup(CLT_Layup $existingLayup, array $incomingLayup): array
    {
        $existingLayup->layers()->delete();

        foreach ($incomingLayup['layers'] as $layer) {
            $existingLayup->layers()->create([
                'layer_order' => $layer['layer_order'],
                'thickness' => $layer['thickness'],
                'width' => $layer['width'],
                'angle' => $layer['angle'],
            ]);
        }

        return [
            'imported' => true,
            'type' => 'overwritten',
            'layup_name' => $existingLayup->name,
            'layup_id' => $existingLayup->id,
            'layer_count' => count($incomingLayup['layers']),
        ];
    }

    private function duplicateLayup(CLT_Layup $existingLayup, array $incomingLayup): array
    {
        $supplier = $existingLayup->supplier;
        $newName = $incomingLayup['name'] . ' (imported)';

        $counter = 1;
        while ($supplier->layups()->where('name', $newName)->exists()) {
            $newName = $incomingLayup['name'] . " (imported-{$counter})";
            $counter++;
        }

        $newLayup = $supplier->layups()->create(['name' => $newName]);

        foreach ($incomingLayup['layers'] as $layer) {
            $newLayup->layers()->create([
                'layer_order' => $layer['layer_order'],
                'thickness' => $layer['thickness'],
                'width' => $layer['width'],
                'angle' => $layer['angle'],
            ]);
        }

        return [
            'imported' => true,
            'type' => 'duplicated',
            'original_name' => $existingLayup->name,
            'new_layup_name' => $newLayup->name,
            'layup_id' => $newLayup->id,
            'layer_count' => count($incomingLayup['layers']),
        ];
    }

    public function validateImportData(array $data): array
    {
        $errors = [];

        if (!isset($data['supplier']) || !isset($data['supplier']['name'])) {
            $errors[] = 'Missing required field: supplier.name';
        }

        if (!isset($data['layups']) || !is_array($data['layups'])) {
            $errors[] = 'Missing required field: layups (must be an array)';
        }

        foreach ($data['layups'] ?? [] as $index => $layup) {
            if (!isset($layup['name'])) {
                $errors[] = "Layup at index {$index}: missing required field 'name'";
            }

            if (!isset($layup['layers']) || !is_array($layup['layers'])) {
                $errors[] = "Layup ".($layup['name'] ?? $index).": missing required field 'layers' (must be an array)";
            }

            foreach ($layup['layers'] ?? [] as $layerIndex => $layer) {
                $required = ['layer_order', 'thickness', 'width', 'angle'];
                foreach ($required as $field) {
                    if (!isset($layer[$field])) {
                        $errors[] = "Layup ".($layup['name'] ?? $index).", Layer {$layerIndex}: missing required field '{$field}'";
                    }
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
