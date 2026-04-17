<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Collection;

class ImportService
{
    public const STRATEGY_OVERWRITE = 'overwrite';
    public const STRATEGY_SKIP = 'skip';
    public const STRATEGY_DUPLICATE = 'duplicate';
    public const STRATEGY_REJECT = 'reject';

    private string $conflictStrategy = self::STRATEGY_SKIP;
    private array $conflicts = [];
    private ConflictDetectionService $conflictDetectionService;

    public function __construct()
    {
        $this->conflictDetectionService = new ConflictDetectionService();
    }

    /**
     * Set conflict resolution strategy
     */
    public function setConflictStrategy(string $strategy): self
    {
        if (!in_array($strategy, [self::STRATEGY_OVERWRITE, self::STRATEGY_SKIP, self::STRATEGY_DUPLICATE, self::STRATEGY_REJECT])) {
            throw new \InvalidArgumentException('Invalid conflict strategy');
        }
        $this->conflictStrategy = $strategy;
        return $this;
    }

    /**
     * Import supplier data with conflict handling
     */
    public function import(array $data, ?Supplier $existingSupplier = null): array
    {
        $this->conflicts = [];

        if (!isset($data['name'])) {
            throw new \InvalidArgumentException('Missing supplier name in import data');
        }

        // Get or create supplier
        $supplier = $existingSupplier ?? Supplier::firstOrCreate(['name' => $data['name']]);

        // Process layups
        foreach ($data['layups'] ?? [] as $layupData) {
            $this->processLayup($supplier, $layupData);
        }

        return [
            'supplier' => $supplier,
            'conflicts' => $this->conflicts,
        ];
    }

    /**
     * Process layup data
     */
    private function processLayup(Supplier $supplier, array $layupData): void
    {
        if (!isset($layupData['name'])) {
            return;
        }

        // Check for existing layup
        $existingLayup = $supplier->layups()->where('name', $layupData['name'])->first();

        if ($existingLayup) {
            // Layup exists - process layers with conflict detection
            $this->processLayersWithConflictDetection($existingLayup, $layupData);
        } else {
            // Create new layup
            $layup = $supplier->layups()->create(['name' => $layupData['name']]);
            $this->processLayers($layup, $layupData['layers'] ?? []);
        }
    }

    /**
     * Process layers with conflict detection
     */
    private function processLayersWithConflictDetection($layup, array $layupData): void
    {
        $existingLayupData = [
            'name' => $layup->name,
            'layers' => $layup->layers->map(function ($layer) {
                return [
                    'id' => $layer->id,
                    'layer_order' => $layer->layer_order,
                    'thickness' => (float) $layer->thickness,
                    'width' => (float) $layer->width,
                    'angle' => (float) $layer->angle,
                ];
            })->toArray(),
        ];

        $incomingLayupData = [
            'name' => $layupData['name'],
            'layers' => $layupData['layers'] ?? [],
        ];

        // Detect conflicts
        $conflicts = $this->conflictDetectionService->detectConflicts($incomingLayupData, $existingLayupData);

        if ($conflicts) {
            if ($this->conflictStrategy === self::STRATEGY_REJECT) {
                $this->conflicts[] = [
                    'status' => 'rejected',
                    'layup_name' => $layup->name,
                    'conflicts' => $conflicts,
                ];
                return;
            }

            // Store conflicts for manual resolution
            foreach ($conflicts as $conflict) {
                $this->conflicts[] = [
                    'status' => 'conflict',
                    'layup_id' => $layup->id,
                    'layup_name' => $layup->name,
                    'conflict' => $conflict,
                ];
            }
        }

        // Process layers based on strategy
        if ($this->conflictStrategy === self::STRATEGY_OVERWRITE) {
            $this->processLayers($layup, $layupData['layers'] ?? [], overwrite: true);
        } else if ($this->conflictStrategy === self::STRATEGY_SKIP) {
            $this->processLayersSkipConflicts($layup, $layupData['layers'] ?? []);
        } else if ($this->conflictStrategy === self::STRATEGY_DUPLICATE) {
            // Create duplicate layup with suffix
            $newLayupName = $layupData['name'] . ' (imported)';
            $newLayup = $layup->supplier->layups()->create(['name' => $newLayupName]);
            $this->processLayers($newLayup, $layupData['layers'] ?? []);
        }
    }

    /**
     * Process layers - skip conflicts
     */
    private function processLayersSkipConflicts($layup, array $layers): void
    {
        foreach ($layers as $layerData) {
            // Check if layer already exists
            $existingLayer = $layup->layers()->where('layer_order', $layerData['layer_order'])->first();

            if (!$existingLayer) {
                // Create new layer if it doesn't exist
                $layup->layers()->create([
                    'layer_order' => $layerData['layer_order'],
                    'thickness' => $layerData['thickness'],
                    'width' => $layerData['width'],
                    'angle' => $layerData['angle'],
                ]);
            }
        }
    }

    /**
     * Process layers - update or create
     */
    private function processLayers($layup, array $layers, bool $overwrite = false): void
    {
        foreach ($layers as $layerData) {
            if ($overwrite) {
                $layup->layers()->updateOrCreate(
                    ['layer_order' => $layerData['layer_order']],
                    [
                        'thickness' => $layerData['thickness'],
                        'width' => $layerData['width'],
                        'angle' => $layerData['angle'],
                    ]
                );
            } else {
                $layup->layers()->firstOrCreate(
                    ['layer_order' => $layerData['layer_order']],
                    [
                        'thickness' => $layerData['thickness'],
                        'width' => $layerData['width'],
                        'angle' => $layerData['angle'],
                    ]
                );
            }
        }
    }

    /**
     * Get detected conflicts
     */
    public function getConflicts(): array
    {
        return $this->conflicts;
    }
}
