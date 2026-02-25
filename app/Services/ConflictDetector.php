<?php

namespace App\Services;

use App\Models\CLT_Layup;

class ConflictDetector
{
    public function detectConflicts(array $importData, $supplier): array
    {
        $conflicts = [];

        foreach ($importData['layups'] as $layupIndex => $incomingLayup) {
            $existingLayup = $supplier->layups()
                ->where('name', 'ilike', "%{$incomingLayup['name']}%")
                ->first();

            if ($existingLayup) {
                $layerConflicts = $this->detectLayerConflicts(
                    $incomingLayup['layers'],
                    $existingLayup
                );

                $conflicts[] = [
                    'type' => 'layup',
                    'layup_index' => $layupIndex,
                    'layup_name' => $incomingLayup['name'],
                    'existing_layup_id' => $existingLayup->id,
                    'layer_conflicts' => [],
                ];

                if (!empty($layerConflicts)) {
                    $conflicts[count($conflicts) - 1]['layer_conflicts'] = $layerConflicts;
                }
            }
        }
        return $conflicts;
    }

    private function detectLayerConflicts(array $incomingLayers, CLT_Layup $existingLayup): array
    {
        $conflicts = [];

        foreach ($incomingLayers as $layerIndex => $incomingLayer) {
            $existingLayer = $existingLayup->layers()
                ->where('layer_order', 'ilike', "%{$incomingLayer['layer_order']}%")
                ->first();

            if ($existingLayer) {
                $differences = [];
                if ((float) $existingLayer->thickness !== (float) $incomingLayer['thickness']) {
                    $differences['thickness'] = [
                        'existing' => (float) $existingLayer->thickness,
                        'incoming' => (float) $incomingLayer['thickness'],
                    ];
                }

                if ((float) $existingLayer->width !== (float) $incomingLayer['width']) {
                    $differences['width'] = [
                        'existing' => (float) $existingLayer->width,
                        'incoming' => (float) $incomingLayer['width'],
                    ];
                }

                if ((float) $existingLayer->angle !== (float) $incomingLayer['angle']) {
                    $differences['angle'] = [
                        'existing' => (float) $existingLayer->angle,
                        'incoming' => (float) $incomingLayer['angle'],
                    ];
                }

                if (!empty($differences)) {
                    $conflicts[] = [
                        'layer_index' => $layerIndex,
                        'layer_order' => $incomingLayer['layer_order'],
                        'existing_layer_id' => $existingLayer->id,
                        'differences' => $differences,
                    ];
                }
            }
        }
        return $conflicts;
    }
}
