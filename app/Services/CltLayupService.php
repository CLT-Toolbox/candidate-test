<?php

namespace App\Services;

use App\Exports\CltLayupExport;
use App\Interfaces\CltLayerRepositoryInterface;
use App\Interfaces\CltLayupRepositoryInterface;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CltLayupService {
    public function __construct(
        protected CltLayupRepositoryInterface $cltLayupRepository,
        protected CltLayerRepositoryInterface $cltLayerRepository
    ) {}

    public function exportToXlxs(Supplier $supplier)
    {
        $file_name = 'Layups_'. $supplier->name . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new CltLayupExport($this->cltLayupRepository, $supplier), $file_name);
    }

    public function checkImport(Supplier $supplier, Collection $data)
    {
        $conflicts = [];
        $readyToImport = [];

        foreach ($data as $row) {
            $layup = $supplier->cltLayups()->where('name', $row['layup_name'])->first();

            if ($layup) {
                $existingLayer = $layup->cltLayers()->where('layer_order', $row['layer_order'])->first();

                if ($existingLayer) {
                    $isDifferent = (
                        bccomp($existingLayer->thickness, $row['thickness'], 2) != 0 ||
                        bccomp($existingLayer->width, $row['width'], 2) != 0 ||
                        bccomp($existingLayer->angle, $row['angle'], 2) != 0
                    );

                    if ($isDifferent) {
                        $conflicts[] = [
                            'id' => $layup->id,
                            'layup_name' => $layup->name,
                            'layer_id' => $existingLayer->id,
                            'order' => $row['layer_order'],
                            'existing' => $existingLayer->only(['thickness', 'width', 'angle']),
                            'incoming' => [
                                'thickness' => $row['thickness'],
                                'width' => $row['width'],
                                'angle' => $row['angle'],
                            ]
                        ];
                    }
                } else {
                    $readyToImport[] = [
                        'layup_name' => $row['layup_name'],
                        'layer_order' => $row['layer_order'],
                        'thickness' => $row['thickness'],
                        'width' => $row['width'],
                        'angle' => $row['angle'],
                    ];
                }
            } else {
                $readyToImport[] = [
                    'layup_name' => $row['layup_name'],
                    'layer_order' => $row['layer_order'],
                    'thickness' => $row['thickness'],
                    'width' => $row['width'],
                    'angle' => $row['angle'],
                ];
            }
        }

        return ['conflicts' => $conflicts, 'clean' => $readyToImport, 'supplier' => $supplier];
    }

    public function resolveImport(Supplier $supplier, array $validatedData)
    {
        if (isset($validatedData['conflicts'])) {
            foreach ($validatedData['conflicts'] as $conflict) {
                if ($conflict['resolution'] === 'overwrite') {
                    $layer = $this->cltLayerRepository->findById($conflict['layer_id']);
                    if ($layer) {
                        $this->cltLayerRepository->update($layer, $conflict['incoming']);
                    }
                }
            }
        }

        $cleanData = json_decode($validatedData['clean_data'], true);
        if (is_array($cleanData)) {
            foreach ($cleanData as $row) {
                $layup = $supplier->cltLayups()->firstOrCreate(['name' => $row['layup_name']]);

                $this->cltLayerRepository->create($layup, [
                    'layer_order' => $row['layer_order'],
                    'thickness' => $row['thickness'],
                    'width' => $row['width'],
                    'angle' => $row['angle'],
                ]);
            }
        }
    }
}
