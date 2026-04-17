<?php

namespace App\Services;

use App\Contracts\Services\ImportServiceInterface;
use App\Exceptions\ImportConflictException;
use App\Http\Resources\SupplierResource;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportService implements ImportServiceInterface
{
    public function import(Supplier $supplier, array $data): array
    {
        $strategy = $data['strategy'] ?? 'reject';
        $report = [
            'strategy' => $strategy,
            'conflicts_found' => 0,
            'conflicts' => [],
            'affected_records' => [
                'layups_created' => 0,
                'layups_reused' => 0,
                'layers_created' => 0,
                'layers_updated' => 0,
                'layers_skipped' => 0,
            ],
        ];

        DB::transaction(function () use ($supplier, $data, $strategy, &$report): void {
            foreach ($data['layups'] as $incomingLayup) {
                $targetLayup = $this->resolveLayup($supplier, $incomingLayup['name'], $strategy, $report);

                foreach ($incomingLayup['layers'] as $incomingLayer) {
                    $existingLayer = $targetLayup->layers()
                        ->where('layer_order', $incomingLayer['layer_order'])
                        ->first();

                    if (! $existingLayer) {
                        $targetLayup->layers()->create($incomingLayer);
                        $report['affected_records']['layers_created']++;

                        continue;
                    }

                    if (! $this->hasLayerConflict($existingLayer, $incomingLayer)) {
                        continue;
                    }

                    $report['conflicts'][] = $this->buildConflictPayload($supplier, $targetLayup, $existingLayer, $incomingLayer);
                    $report['conflicts_found']++;

                    match ($strategy) {
                        'overwrite' => $this->overwriteLayer($existingLayer, $incomingLayer, $report),
                        'skip' => $report['affected_records']['layers_skipped']++,
                        'reject' => null,
                        default => null,
                    };
                }
            }

            if ($strategy === 'reject' && $report['conflicts_found'] > 0) {
                Log::warning('Layup import rejected because conflicts were detected.', [
                    'supplier_id' => $supplier->id,
                    'report' => $report,
                ]);

                throw new ImportConflictException($report);
            }
        });

        return [
            'supplier' => (new SupplierResource($supplier->fresh()->load('layups.layers')))->resolve(),
            'resolution_applied' => $strategy,
            'conflicts_found' => $report['conflicts_found'],
            'conflicts' => $report['conflicts'],
            'affected_records' => $report['affected_records'],
        ];
    }

    protected function resolveLayup(Supplier $supplier, string $name, string $strategy, array &$report): Layup
    {
        $existingLayup = $supplier->layups()->where('name', $name)->first();

        if (! $existingLayup) {
            $report['affected_records']['layups_created']++;

            return $supplier->layups()->create(['name' => $name]);
        }

        if ($strategy === 'duplicate_layup') {
            $report['affected_records']['layups_created']++;

            return $supplier->layups()->create([
                'name' => $this->generateImportedLayupName($supplier, $name),
            ]);
        }

        $report['affected_records']['layups_reused']++;

        return $existingLayup;
    }

    protected function hasLayerConflict(Layer $existingLayer, array $incomingLayer): bool
    {
        return (int) $existingLayer->layer_order === (int) $incomingLayer['layer_order']
            && (
                (float) $existingLayer->thickness !== (float) $incomingLayer['thickness']
                || (float) $existingLayer->width !== (float) $incomingLayer['width']
                || (float) $existingLayer->angle !== (float) $incomingLayer['angle']
            );
    }

    protected function overwriteLayer(Layer $existingLayer, array $incomingLayer, array &$report): void
    {
        $existingLayer->update($incomingLayer);
        $report['affected_records']['layers_updated']++;
    }

    protected function buildConflictPayload(
        Supplier $supplier,
        Layup $layup,
        Layer $existingLayer,
        array $incomingLayer,
    ): array {
        return [
            'supplier_id' => $supplier->id,
            'layup_id' => $layup->id,
            'layup_name' => $layup->name,
            'layer_order' => $existingLayer->layer_order,
            'existing' => [
                'thickness' => $existingLayer->thickness,
                'width' => $existingLayer->width,
                'angle' => $existingLayer->angle,
            ],
            'incoming' => [
                'thickness' => (float) $incomingLayer['thickness'],
                'width' => (float) $incomingLayer['width'],
                'angle' => (float) $incomingLayer['angle'],
            ],
        ];
    }

    protected function generateImportedLayupName(Supplier $supplier, string $baseName): string
    {
        $candidate = $baseName.' (imported)';
        $index = 1;

        while ($supplier->layups()->where('name', $candidate)->exists()) {
            $index++;
            $candidate = sprintf('%s (imported %d)', $baseName, $index);
        }

        return $candidate;
    }
}
