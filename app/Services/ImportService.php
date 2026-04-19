<?php

namespace App\Services;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ImportService
{
    public function analyze(Supplier $supplier, array $data): array
    {
        $analysis = [
            'matches' => [],
            'new_items' => [],
            'conflicts' => [],
            'summary' => [
                'total_layups' => count($data['layups'] ?? []),
                'conflict_count' => 0,
                'new_count' => 0,
            ],
        ];

        $existingLayups = $supplier->layups()->with('layers')->get()->keyBy('name');

        foreach ($data['layups'] ?? [] as $incomingLayup) {
            $name = $incomingLayup['name'];
            
            if ($existingLayups->has($name)) {
                $existing = $existingLayups->get($name);
                $layupConflicts = $this->detectLayerConflicts($existing, $incomingLayup['layers'] ?? []);
                
                if (count($layupConflicts) > 0) {
                    $analysis['conflicts'][] = [
                        'type' => 'layup',
                        'id' => $existing->id,
                        'name' => $name,
                        'existing' => $existing,
                        'incoming' => $incomingLayup,
                        'layer_conflicts' => $layupConflicts,
                    ];
                    $analysis['summary']['conflict_count']++;
                } else {
                    $analysis['matches'][] = $name;
                }
            } else {
                $analysis['new_items'][] = $incomingLayup;
                $analysis['summary']['new_count']++;
            }
        }

        return $analysis;
    }

    protected function detectLayerConflicts(CltLayup $existing, array $incomingLayers): array
    {
        $conflicts = [];
        $existingLayers = $existing->layers->keyBy('layer_order');

        foreach ($incomingLayers as $incoming) {
            $order = $incoming['layer_order'];
            
            if ($existingLayers->has($order)) {
                $exist = $existingLayers->get($order);
                
                $isDifferent = 
                    (float)$exist->thickness !== (float)$incoming['thickness'] ||
                    (float)$exist->width !== (float)$incoming['width'] ||
                    (float)$exist->angle !== (float)$incoming['angle'];

                if ($isDifferent) {
                    $conflicts[] = [
                        'layer_order' => $order,
                        'existing' => [
                            'thickness' => $exist->thickness,
                            'width' => $exist->width,
                            'angle' => $exist->angle,
                        ],
                        'incoming' => [
                            'thickness' => $incoming['thickness'],
                            'width' => $incoming['width'],
                            'angle' => $incoming['angle'],
                        ]
                    ];
                }
            }
        }

        return $conflicts;
    }

    public function execute(Supplier $supplier, array $data, array $resolutions, string $strategy, bool $dryRun = false): void
    {
        DB::beginTransaction();
        try {
            $existingLayups = $supplier->layups()->get()->keyBy('name');

            foreach ($data['layups'] ?? [] as $incoming) {
                $name = $incoming['name'];
                $resolution = $resolutions[$name] ?? $strategy;

                if ($resolution === 'skip' && $existingLayups->has($name)) {
                    continue;
                }

                if ($resolution === 'duplicate' && $existingLayups->has($name)) {
                    $incoming['name'] .= ' (Imported ' . date('Y-m-d H:i') . ')';
                }

                $layup = $supplier->layups()->updateOrCreate(
                    ['name' => $incoming['name']],
                    ['name' => $incoming['name']]
                );

                if ($resolution === 'overwrite') {
                    $layup->layers()->delete();
                }

                foreach ($incoming['layers'] ?? [] as $layerData) {
                    if ($resolution === 'overwrite' || !$layup->layers()->where('layer_order', $layerData['layer_order'])->exists()) {
                        $layup->layers()->updateOrCreate(
                            ['layer_order' => $layerData['layer_order']],
                            $layerData
                        );
                    }
                }
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
