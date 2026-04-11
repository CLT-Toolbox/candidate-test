<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\CltLayupRepositoryInterface;
use App\Repositories\Contracts\CltLayerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ImportExportService
{
    public function __construct(
        private CltLayupRepositoryInterface $layupRepository,
        private CltLayerRepositoryInterface $layerRepository,
    ) {}

    public function export(Supplier $supplier): array
    {
        $supplier->load('layups.layers');

        return [
            'supplier' => [
                'id'   => $supplier->id,
                'name' => $supplier->name,
            ],
            'layups' => $supplier->layups->map(function ($layup) {
                return [
                    'name'   => $layup->name,
                    'layers' => $layup->layers->map(fn($layer) => [
                        'layer_order' => $layer->layer_order,
                        'thickness'   => $layer->thickness,
                        'width'       => $layer->width,
                        'angle'       => $layer->angle,
                    ])->values()->toArray(),
                ];
            })->values()->toArray(),
        ];
    }

    public function detectConflicts(Supplier $supplier, array $importData): array
    {
        $conflicts = [];

        foreach ($importData['layups'] as $layupData) {
            $existingLayup = $this->layupRepository->findByNameAndSupplier(
                $layupData['name'],
                $supplier->id
            );

            if (!$existingLayup) continue;

            $layupConflicts = [];

            foreach ($layupData['layers'] as $layerData) {
                $existingLayer = $this->layerRepository->findByOrderAndLayup(
                    $layerData['layer_order'],
                    $existingLayup->id
                );

                if (!$existingLayer) continue;

                $diffFields = [];
                foreach (['thickness', 'width', 'angle'] as $field) {
                    if ((float) $existingLayer->$field !== (float) $layerData[$field]) {
                        $diffFields[] = $field;
                    }
                }

                if (!empty($diffFields)) {
                    $layupConflicts[] = [
                        'layer_order'   => $layerData['layer_order'],
                        'existing'      => [
                            'layer_order' => $existingLayer->layer_order,
                            'thickness'   => $existingLayer->thickness,
                            'width'       => $existingLayer->width,
                            'angle'       => $existingLayer->angle,
                        ],
                        'incoming'      => [
                            'layer_order' => $layerData['layer_order'],
                            'thickness'   => $layerData['thickness'],
                            'width'       => $layerData['width'],
                            'angle'       => $layerData['angle'],
                        ],
                        'diff_fields'   => $diffFields,
                    ];
                }
            }

            if (!empty($layupConflicts)) {
                $conflicts[] = [
                    'layup_name' => $layupData['name'],
                    'layup_id'   => $existingLayup->id,
                    'layers'     => $layupConflicts,
                ];
            }
        }

        return $conflicts;
    }

    public function import(Supplier $supplier, array $importData, string $strategy, array $resolutions = []): array
    {
        return DB::transaction(function () use ($supplier, $importData, $strategy, $resolutions) {
            $results = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'duplicated' => 0];

            foreach ($importData['layups'] as $layupData) {
                $existingLayup = $this->layupRepository->findByNameAndSupplier(
                    $layupData['name'],
                    $supplier->id
                );

                if ($strategy === 'duplicate') {
                    $layup = $this->layupRepository->create([
                        'supplier_id' => $supplier->id,
                        'name'        => $layupData['name'] . ' (imported)',
                    ]);
                    $results['duplicated']++;
                } elseif (!$existingLayup) {
                    $layup = $this->layupRepository->create([
                        'supplier_id' => $supplier->id,
                        'name'        => $layupData['name'],
                    ]);
                    $results['created']++;
                } else {
                    $layup = $existingLayup;
                }

                foreach ($layupData['layers'] as $layerData) {
                    $existingLayer = $this->layerRepository->findByOrderAndLayup(
                        $layerData['layer_order'],
                        $layup->id
                    );

                    $hasConflict = $existingLayer && (
                        (float) $existingLayer->thickness !== (float) $layerData['thickness'] ||
                        (float) $existingLayer->width !== (float) $layerData['width'] ||
                        (float) $existingLayer->angle !== (float) $layerData['angle']
                    );

                    if (!$hasConflict) {
                        if (!$existingLayer) {
                            $this->layerRepository->create(array_merge($layerData, ['layup_id' => $layup->id]));
                            $results['created']++;
                        }
                        continue;
                    }

                    // Handle conflict based on strategy
                    if ($strategy === 'manual') {
                        $key = $layupData['name'] . '_' . $layerData['layer_order'];
                        $resolution = $resolutions[$key] ?? 'skip';
                        if ($resolution === 'accept') {
                            $this->layerRepository->update($existingLayer, $layerData);
                            $results['updated']++;
                        } else {
                            $results['skipped']++;
                        }
                    } elseif ($strategy === 'overwrite') {
                        $this->layerRepository->update($existingLayer, $layerData);
                        $results['updated']++;
                    } elseif ($strategy === 'skip') {
                        $results['skipped']++;
                    } elseif ($strategy === 'reject') {
                        throw new \Exception('Import rejected due to conflicts.');
                    }
                }
            }

            return $results;
        });
    }
}
