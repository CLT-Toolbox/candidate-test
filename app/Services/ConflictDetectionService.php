<?php

namespace App\Services;

class ConflictDetectionService
{
    const CONFLICT_TYPE_LAYER = 'layer';
    const CONFLICT_TYPE_LAYUP = 'layup';

    /**
     * Detect conflicts between existing and incoming data
     */
    public function detectConflicts(array $incomingData, array $existingData = null): array
    {
        $conflicts = [];

        if (!$existingData) {
            return $conflicts;
        }

        // Check layup-level conflicts
        if ($incomingData['name'] !== $existingData['name']) {
            // Different layup names for same position
        }

        // Check layer-level conflicts
        $incomingLayers = collect($incomingData['layers'] ?? []);
        $existingLayers = collect($existingData['layers'] ?? []);

        foreach ($incomingLayers as $incomingLayer) {
            $existingLayer = $existingLayers->firstWhere('layer_order', $incomingLayer['layer_order']);

            if ($existingLayer) {
                // Check if any field differs
                if (
                    (float) $incomingLayer['thickness'] !== (float) $existingLayer['thickness'] ||
                    (float) $incomingLayer['width'] !== (float) $existingLayer['width'] ||
                    (float) $incomingLayer['angle'] !== (float) $existingLayer['angle']
                ) {
                    $conflicts[] = [
                        'type' => self::CONFLICT_TYPE_LAYER,
                        'layer_order' => $incomingLayer['layer_order'],
                        'incoming' => $incomingLayer,
                        'existing' => $existingLayer,
                    ];
                }
            }
        }

        return $conflicts;
    }
}
