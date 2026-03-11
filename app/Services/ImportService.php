<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;

class ImportService
{
    public function import(array $data, string $conflictStrategy = 'overwrite'): array
    {
        $result = [
            'success' => true,
            'message' => 'Import successful',
            'conflicts' => []
        ];

        try {
            $supplier = Supplier::firstOrCreate(
                ['name' => $data['supplier']['name']],
                ['name' => $data['supplier']['name']]
            );

            if (isset($data['layups']) && is_array($data['layups'])) {
                foreach ($data['layups'] as $layupData) {
                    $this->importLayup($supplier, $layupData, $conflictStrategy, $result);
                }
            }

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'conflicts' => $result['conflicts']
            ];
        }
    }

    private function importLayup(Supplier $supplier, array $layupData, string $strategy, array &$result)
    {
        $existingLayup = $supplier->layups()
            ->where('name', $layupData['name'])
            ->first();

        if ($existingLayup) {
            $result['conflicts'][] = [
                'type' => 'layup',
                'name' => $layupData['name'],
                'action' => $strategy
            ];

            if ($strategy === 'skip') {
                return $existingLayup;
            } elseif ($strategy === 'duplicate') {
                $layupData['name'] .= ' (imported)';
                $existingLayup = null;
            } elseif ($strategy === 'reject') {
                throw new \Exception('Conflict rejected: Layup "' . $layupData['name'] . '" already exists');
            }
        }

        $layup = $existingLayup ?? $supplier->layups()->create(['name' => $layupData['name']]);

        if (isset($layupData['layers']) && is_array($layupData['layers'])) {
            foreach ($layupData['layers'] as $layerData) {
                $this->importLayer($layup, $layerData, $strategy, $result);
            }
        }

        return $layup;
    }

    private function importLayer(CltLayup $layup, array $layerData, string $strategy, array &$result)
    {
        $existingLayer = $layup->layers()
            ->where('layer_order', $layerData['layer_order'])
            ->first();

        if ($existingLayer) {
            $hasConflict = (
                $existingLayer->thickness != $layerData['thickness'] ||
                $existingLayer->width != $layerData['width'] ||
                $existingLayer->angle != $layerData['angle']
            );

            if ($hasConflict) {
                $result['conflicts'][] = [
                    'type' => 'layer',
                    'layup' => $layup->name,
                    'layer_order' => $layerData['layer_order'],
                    'action' => $strategy
                ];

                if ($strategy === 'skip') {
                    return;
                } elseif ($strategy === 'reject') {
                    throw new \Exception('Conflict rejected: Layer order ' . $layerData['layer_order']);
                }
            }

            if ($strategy === 'overwrite') {
                $existingLayer->update($layerData);
                return;
            }
        }

        $layup->layers()->create($layerData);
    }
}