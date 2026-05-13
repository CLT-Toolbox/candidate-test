<?php

namespace App\Services;

use App\Exceptions\ImportConflictException;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\LayerRepositoryInterface;
use App\Repositories\Contracts\LayupRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SupplierImportExportService
{
    public const STRATEGY_OVERWRITE = 'overwrite';
    public const STRATEGY_SKIP = 'skip';
    public const STRATEGY_DUPLICATE_LAYUP = 'duplicate_layup';
    public const STRATEGY_REJECT = 'reject';

    public function __construct(
        private readonly LayupRepositoryInterface $layupRepository,
        private readonly LayerRepositoryInterface $layerRepository,
    ) {
    }

    /**
     * Export data supplier lengkap dengan semua layups dan layers.
     * Menggunakan eager loading untuk optimasi query.
     * Return format array dengan struktur hierarki.
     * 
     * @return array<string, mixed>
     */
    public function exportBySupplier(Supplier $supplier): array
    {
        $supplier->load(['layups.layers' => fn ($query) => $query->orderBy('layer_order')]);

        return [
            'supplier' => Arr::only($supplier->toArray(), ['id', 'name', 'code', 'address']),
            'layups' => $supplier->layups->map(function (Layup $layup): array {
                return [
                    'id' => $layup->id,
                    'name' => $layup->name,
                    'description' => $layup->description,
                    'layers' => $layup->layers->map(function (Layer $layer): array {
                        return [
                            'id' => $layer->id,
                            'layer_order' => $layer->layer_order,
                            'thickness' => (float) $layer->thickness,
                            'width' => (float) $layer->width,
                            'angle' => (float) $layer->angle,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    /**
     * Import data layups dan layers ke supplier dengan strategi conflict resolution.
     * 
     * Strategi yang didukung:
     * - STRATEGY_OVERWRITE: Data incoming menimpa data existing
     * - STRATEGY_SKIP: Lewati konflik, pertahankan data existing
     * - STRATEGY_DUPLICATE_LAYUP: Buat layup baru dengan suffix "(imported)"
     * - STRATEGY_REJECT: Tolak seluruh import jika ada konflik
     * 
     * Alur kerja:
     * 1. Loop setiap layup dari payload
     * 2. Cek apakah layup dengan nama sama sudah ada
     * 3. Jika tidak ada, buat layup baru + semua layers
     * 4. Jika ada, proses setiap layer untuk deteksi konflik
     * 5. Deteksi konflik: Bandingkan thickness, width, angle
     * 6. Terapkan strategy sesuai pilihan
     * 
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function importBySupplier(Supplier $supplier, array $payload, string $strategy): array
    {
        $this->guardStrategy($strategy);

        $stats = [
            'created_layups' => 0,
            'created_layers' => 0,
            'updated_layers' => 0,
            'skipped_layers' => 0,
            'conflicts' => [],
        ];

        DB::transaction(function () use ($supplier, $payload, $strategy, &$stats): void {
            $layups = Arr::get($payload, 'layups', []);

            foreach ($layups as $layupData) {
                $layupName = (string) Arr::get($layupData, 'name');
                if ($layupName === '') {
                    continue;
                }

                $existingLayup = $this->layupRepository->findByNameInSupplier($supplier, $layupName);

                if ($existingLayup === null) {
                    $newLayup = $this->layupRepository->createForSupplier($supplier, [
                        'name' => $layupName,
                        'description' => Arr::get($layupData, 'description'),
                    ]);
                    $stats['created_layups']++;

                    foreach ((array) Arr::get($layupData, 'layers', []) as $incomingLayer) {
                        $this->createLayer($newLayup, $incomingLayer);
                        $stats['created_layers']++;
                    }

                    continue;
                }

                if ($strategy === self::STRATEGY_DUPLICATE_LAYUP) {
                    $duplicatedLayup = $this->layupRepository->createForSupplier($supplier, [
                        'name' => $this->generateDuplicatedLayupName($supplier, $layupName),
                        'description' => Arr::get($layupData, 'description'),
                    ]);
                    $stats['created_layups']++;

                    foreach ((array) Arr::get($layupData, 'layers', []) as $incomingLayer) {
                        $this->createLayer($duplicatedLayup, $incomingLayer);
                        $stats['created_layers']++;
                    }

                    continue;
                }

                foreach ((array) Arr::get($layupData, 'layers', []) as $incomingLayer) {
                    $this->processLayerImport(
                        layup: $existingLayup,
                        incomingLayer: (array) $incomingLayer,
                        strategy: $strategy,
                        stats: $stats,
                    );
                }
            }

            if ($strategy === self::STRATEGY_REJECT && count($stats['conflicts']) > 0) {
                throw new ImportConflictException($stats['conflicts']);
            }
        });

        return [
            'strategy' => $strategy,
            'summary' => [
                'created_layups' => $stats['created_layups'],
                'created_layers' => $stats['created_layers'],
                'updated_layers' => $stats['updated_layers'],
                'skipped_layers' => $stats['skipped_layers'],
                'conflicts_count' => count($stats['conflicts']),
            ],
            'conflicts' => $stats['conflicts'],
        ];
    }

    /**
     * Menerapkan resolusi manual untuk konflik yang pending.
     * User memilih 'keep_existing' atau 'accept_incoming' untuk setiap konflik.
     * Menggunakan database transaction untuk memastikan atomicity.
     * 
     * @param array<int, array<string, mixed>> $conflicts
     * @param array<int|string, array<string, mixed>> $resolutions
     * @return array<string, mixed>
     */
    public function resolveConflicts(Supplier $supplier, array $conflicts, array $resolutions): array
    {
        $summary = [
            'resolved_accept_incoming' => 0,
            'resolved_keep_existing' => 0,
            'missing_targets' => 0,
            'total_conflicts' => count($conflicts),
        ];

        DB::transaction(function () use ($supplier, $conflicts, $resolutions, &$summary): void {
            foreach ($conflicts as $index => $conflict) {
                $decision = (string) Arr::get($resolutions, $index.'.decision', 'keep_existing');

                if ($decision === 'keep_existing') {
                    $summary['resolved_keep_existing']++;

                    continue;
                }

                $layupName = (string) Arr::get($conflict, 'layup_name');
                $layerOrder = (int) Arr::get($conflict, 'layer_order');
                $incoming = (array) Arr::get($conflict, 'incoming', []);

                $layup = $this->layupRepository->findByNameInSupplier($supplier, $layupName);
                if ($layup === null) {
                    $summary['missing_targets']++;

                    continue;
                }

                $layer = $this->layerRepository->findByOrderInLayup($layup, $layerOrder);
                if ($layer === null) {
                    $summary['missing_targets']++;

                    continue;
                }

                $this->layerRepository->update($layer, [
                    'thickness' => (float) Arr::get($incoming, 'thickness', $layer->thickness),
                    'width' => (float) Arr::get($incoming, 'width', $layer->width),
                    'angle' => (float) Arr::get($incoming, 'angle', $layer->angle),
                ]);

                $summary['resolved_accept_incoming']++;
            }
        });

        return [
            'strategy' => 'manual_resolution',
            'summary' => $summary,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Helper method untuk membuat layer baru dalam layup.
     * 
     * @param array<string, mixed> $incomingLayer
     */
    private function createLayer(Layup $layup, array $incomingLayer): void
    {
        $this->layerRepository->createForLayup($layup, [
            'layer_order' => (int) Arr::get($incomingLayer, 'layer_order'),
            'thickness' => (float) Arr::get($incomingLayer, 'thickness'),
            'width' => (float) Arr::get($incomingLayer, 'width'),
            'angle' => (float) Arr::get($incomingLayer, 'angle'),
        ]);
    }

    /**
     * Helper method untuk memproses import satu layer.
     * Mendeteksi konflik dan menerapkan strategy.
     * 
     * Alur kerja:
     * 1. Cek apakah layer dengan layer_order sama sudah ada
     * 2. Jika tidak ada, buat layer baru
     * 3. Jika ada, bandingkan field (thickness, width, angle)
     * 4. Jika ada perbedaan, catat sebagai konflik
     * 5. Terapkan strategy sesuai pilihan
     * 
     * @param array<string, mixed> $incomingLayer
     * @param array<string, mixed> $stats
     */
    private function processLayerImport(Layup $layup, array $incomingLayer, string $strategy, array &$stats): void
    {
        $layerOrder = (int) Arr::get($incomingLayer, 'layer_order');
        $existingLayer = $this->layerRepository->findByOrderInLayup($layup, $layerOrder);

        if ($existingLayer === null) {
            $this->createLayer($layup, $incomingLayer);
            $stats['created_layers']++;

            return;
        }

        $incomingValues = [
            'thickness' => (float) Arr::get($incomingLayer, 'thickness'),
            'width' => (float) Arr::get($incomingLayer, 'width'),
            'angle' => (float) Arr::get($incomingLayer, 'angle'),
        ];

        $existingValues = [
            'thickness' => (float) $existingLayer->thickness,
            'width' => (float) $existingLayer->width,
            'angle' => (float) $existingLayer->angle,
        ];

        $diffFields = array_keys(array_filter([
            'thickness' => $existingValues['thickness'] !== $incomingValues['thickness'],
            'width' => $existingValues['width'] !== $incomingValues['width'],
            'angle' => $existingValues['angle'] !== $incomingValues['angle'],
        ]));

        if (count($diffFields) === 0) {
            return;
        }

        $conflict = [
            'layup_name' => $layup->name,
            'layer_order' => $layerOrder,
            'diff_fields' => $diffFields,
            'existing' => $existingValues,
            'incoming' => $incomingValues,
        ];

        $stats['conflicts'][] = $conflict;

        if ($strategy === self::STRATEGY_SKIP || $strategy === self::STRATEGY_REJECT) {
            $stats['skipped_layers']++;

            return;
        }

        if ($strategy === self::STRATEGY_OVERWRITE) {
            $this->layerRepository->update($existingLayer, $incomingValues);
            $stats['updated_layers']++;
        }
    }

    /**
     * Validasi strategy yang dipilih.
     * Throw 422 error jika strategy tidak valid.
     */
    private function guardStrategy(string $strategy): void
    {
        if (! in_array($strategy, [
            self::STRATEGY_OVERWRITE,
            self::STRATEGY_SKIP,
            self::STRATEGY_DUPLICATE_LAYUP,
            self::STRATEGY_REJECT,
        ], true)) {
            abort(422, 'Invalid import strategy.');
        }
    }

    /**
     * Generate nama unik untuk layup yang diduplikasi.
     * Tambahkan suffix "(imported)".
     * Jika sudah ada, tambahkan counter "(imported) 2", "(imported) 3", dst.
     */
    private function generateDuplicatedLayupName(Supplier $supplier, string $baseName): string
    {
        $candidate = $baseName.' (imported)';

        if ($this->layupRepository->findByNameInSupplier($supplier, $candidate) === null) {
            return $candidate;
        }

        $counter = 2;
        while ($this->layupRepository->findByNameInSupplier($supplier, $candidate.' '.$counter) !== null) {
            $counter++;
        }

        return $candidate.' '.$counter;
    }
}
