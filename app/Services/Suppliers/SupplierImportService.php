<?php

namespace App\Services\Suppliers;

use App\Models\Supplier;
use App\Repositories\Contracts\LayerRepositoryInterface;
use App\Repositories\Contracts\LayupRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SupplierImportService
{
    public const STRATEGY_MANUAL = 'manual';

    public const STRATEGY_OVERWRITE = 'overwrite';

    public const STRATEGY_SKIP = 'skip';

    public const STRATEGY_REJECT = 'reject';

    public function __construct(
        private readonly SupplierRepositoryInterface $suppliers,
        private readonly LayupRepositoryInterface $layups,
        private readonly LayerRepositoryInterface $layers,
    ) {
    }

    public function prepareImport(Supplier $supplier, array $payload): array
    {
        $validated = $this->validatePayload($payload);
        $supplier = $this->suppliers->findWithRelations($supplier);

        $existingLayups = $supplier->layups->keyBy(
            fn ($layup): string => $this->layupLookupKey($layup->name),
        );

        $preparedLayups = [];
        $conflicts = [];

        foreach ($validated['layups'] as $layupData) {
            $layupKey = $this->layupLookupKey($layupData['name']);
            $existingLayup = $existingLayups->get($layupKey);
            $existingLayers = $existingLayup?->layers->keyBy('layer_order') ?? collect();
            $preparedLayers = [];

            foreach ($layupData['layers'] as $layerData) {
                $incomingLayer = $this->normalizeLayer($layerData);
                $existingLayer = $existingLayers->get($incomingLayer['layer_order']);

                if (! $existingLayer) {
                    $preparedLayers[] = [
                        'action' => 'create',
                        'existing_layer_id' => null,
                        'incoming' => $incomingLayer,
                    ];

                    continue;
                }

                $differences = $this->layerDifferences($existingLayer->only([
                    'thickness',
                    'width',
                    'angle',
                ]), $incomingLayer);

                if ($differences === []) {
                    $preparedLayers[] = [
                        'action' => 'noop',
                        'existing_layer_id' => $existingLayer->id,
                        'incoming' => $incomingLayer,
                    ];

                    continue;
                }

                $conflictKey = $layupKey.'::'.$incomingLayer['layer_order'];

                $conflicts[] = [
                    'key' => $conflictKey,
                    'layup_name' => $layupData['name'],
                    'layer_order' => $incomingLayer['layer_order'],
                    'differences' => $differences,
                    'existing' => [
                        'id' => $existingLayer->id,
                        'layer_order' => $existingLayer->layer_order,
                        'thickness' => $this->formatDecimal($existingLayer->thickness),
                        'width' => $this->formatDecimal($existingLayer->width),
                        'angle' => $this->formatDecimal($existingLayer->angle),
                    ],
                    'incoming' => [
                        'layer_order' => $incomingLayer['layer_order'],
                        'thickness' => $this->formatDecimal($incomingLayer['thickness']),
                        'width' => $this->formatDecimal($incomingLayer['width']),
                        'angle' => $this->formatDecimal($incomingLayer['angle']),
                    ],
                ];

                $preparedLayers[] = [
                    'action' => 'conflict',
                    'conflict_key' => $conflictKey,
                    'existing_layer_id' => $existingLayer->id,
                    'incoming' => $incomingLayer,
                ];
            }

            $preparedLayups[] = [
                'name' => $layupData['name'],
                'action' => $existingLayup ? 'update' : 'create',
                'existing_layup_id' => $existingLayup?->id,
                'layers' => $preparedLayers,
            ];
        }

        return [
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'source_supplier_name' => data_get($validated, 'supplier.name'),
            'layups' => $preparedLayups,
            'conflicts' => $conflicts,
        ];
    }

    public function commitPreparedImport(
        Supplier $supplier,
        array $prepared,
        string $strategy,
        array $manualResolutions = [],
    ): array {
        if (($prepared['supplier_id'] ?? null) !== $supplier->id) {
            throw ValidationException::withMessages([
                'import_file' => 'Import preview does not belong to this supplier.',
            ]);
        }

        $conflicts = $prepared['conflicts'] ?? [];

        if ($strategy === self::STRATEGY_REJECT && $conflicts !== []) {
            throw ValidationException::withMessages([
                'import_file' => 'Import was rejected because conflicts were detected.',
            ]);
        }

        return DB::transaction(function () use ($supplier, $prepared, $strategy, $manualResolutions): array {
            $summary = [
                'strategy' => $strategy,
                'created_layups' => 0,
                'created_layers' => 0,
                'updated_layers' => 0,
                'accepted_incoming_conflicts' => 0,
                'kept_existing_conflicts' => 0,
                'unchanged_layers' => 0,
                'conflicts' => count($prepared['conflicts'] ?? []),
            ];

            foreach ($prepared['layups'] as $preparedLayup) {
                $layup = $preparedLayup['action'] === 'create'
                    ? tap($this->layups->createForSupplier($supplier, [
                        'name' => $preparedLayup['name'],
                    ]), function () use (&$summary): void {
                        $summary['created_layups']++;
                    })
                    : $this->layups->findBySupplierAndName($supplier, $preparedLayup['name']);

                if (! $layup) {
                    $layup = $this->layups->createForSupplier($supplier, [
                        'name' => $preparedLayup['name'],
                    ]);
                    $summary['created_layups']++;
                }

                foreach ($preparedLayup['layers'] as $preparedLayer) {
                    $incoming = $preparedLayer['incoming'];

                    if ($preparedLayer['action'] === 'create') {
                        $this->layers->createForLayup($layup, $incoming);
                        $summary['created_layers']++;

                        continue;
                    }

                    if ($preparedLayer['action'] === 'noop') {
                        $summary['unchanged_layers']++;

                        continue;
                    }

                    $existingLayer = $this->layers->findByLayupAndOrder($layup, (int) $incoming['layer_order']);

                    if (! $existingLayer) {
                        $this->layers->createForLayup($layup, $incoming);
                        $summary['created_layers']++;

                        continue;
                    }

                    $decision = $this->resolveConflictDecision(
                        $strategy,
                        $preparedLayer['conflict_key'],
                        $manualResolutions,
                    );

                    if ($decision === 'accept_incoming') {
                        $this->layers->update($existingLayer, $incoming);
                        $summary['updated_layers']++;
                        $summary['accepted_incoming_conflicts']++;

                        continue;
                    }

                    $summary['kept_existing_conflicts']++;
                }
            }

            return $summary;
        });
    }

    public function validatePayload(array $payload): array
    {
        $validator = Validator::make($payload, [
            'supplier' => ['nullable', 'array'],
            'supplier.name' => ['nullable', 'string', 'max:255'],
            'layups' => ['required', 'array', 'min:1'],
            'layups.*.name' => ['required', 'string', 'max:255'],
            'layups.*.layers' => ['required', 'array', 'min:1'],
            'layups.*.layers.*.layer_order' => ['required', 'integer', 'min:1'],
            'layups.*.layers.*.thickness' => ['required', 'numeric', 'gt:0'],
            'layups.*.layers.*.width' => ['required', 'numeric', 'gt:0'],
            'layups.*.layers.*.angle' => ['required', 'numeric', 'between:-360,360'],
        ]);

        $validated = $validator->validate();
        $errors = [];
        $seenLayups = [];

        foreach ($validated['layups'] as $layupIndex => $layup) {
            $layupKey = $this->layupLookupKey($layup['name']);

            if (isset($seenLayups[$layupKey])) {
                $errors["layups.{$layupIndex}.name"][] = 'Layup names must be unique inside the import file.';
            }

            $seenLayups[$layupKey] = true;
            $seenLayerOrders = [];

            foreach ($layup['layers'] as $layerIndex => $layer) {
                $layerOrder = (int) $layer['layer_order'];

                if (isset($seenLayerOrders[$layerOrder])) {
                    $errors["layups.{$layupIndex}.layers.{$layerIndex}.layer_order"][] = 'Layer order must be unique inside each layup.';
                }

                $seenLayerOrders[$layerOrder] = true;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $validated['layups'] = collect($validated['layups'])
            ->map(fn (array $layup): array => [
                'name' => trim($layup['name']),
                'layers' => collect($layup['layers'])
                    ->sortBy('layer_order')
                    ->values()
                    ->map(fn (array $layer): array => $this->normalizeLayer($layer))
                    ->all(),
            ])
            ->all();

        return $validated;
    }

    private function resolveConflictDecision(string $strategy, string $conflictKey, array $manualResolutions): string
    {
        return match ($strategy) {
            self::STRATEGY_OVERWRITE => 'accept_incoming',
            self::STRATEGY_SKIP => 'keep_existing',
            self::STRATEGY_MANUAL => $manualResolutions[$conflictKey] ?? throw ValidationException::withMessages([
                'resolutions' => "A resolution is required for conflict [{$conflictKey}].",
            ]),
            self::STRATEGY_REJECT => throw ValidationException::withMessages([
                'import_file' => 'Conflicts must be rejected before commit.',
            ]),
            default => throw ValidationException::withMessages([
                'conflict_strategy' => 'Unsupported conflict strategy.',
            ]),
        };
    }

    private function layerDifferences(array $existing, array $incoming): array
    {
        $differences = [];

        foreach (['thickness', 'width', 'angle'] as $field) {
            $existingValue = $this->formatDecimal($existing[$field]);
            $incomingValue = $this->formatDecimal($incoming[$field]);

            if ($existingValue !== $incomingValue) {
                $differences[$field] = [
                    'existing' => $existingValue,
                    'incoming' => $incomingValue,
                ];
            }
        }

        return $differences;
    }

    private function normalizeLayer(array $layer): array
    {
        return [
            'layer_order' => (int) $layer['layer_order'],
            'thickness' => $this->formatDecimal($layer['thickness']),
            'width' => $this->formatDecimal($layer['width']),
            'angle' => $this->formatDecimal($layer['angle']),
        ];
    }

    private function formatDecimal(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function layupLookupKey(string $name): string
    {
        return Str::lower(trim($name));
    }
}
