<?php

namespace App\Services\Suppliers;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;

class SupplierExportService
{
    public function __construct(
        private readonly SupplierRepositoryInterface $suppliers,
    ) {
    }

    public function buildPayload(Supplier $supplier): array
    {
        $supplier = $this->suppliers->findWithRelations($supplier);

        return [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ],
            'exported_at' => now()->toIso8601String(),
            'layups' => $supplier->layups
                ->sortBy('name')
                ->values()
                ->map(fn (Layup $layup): array => [
                    'name' => $layup->name,
                    'layers' => $layup->layers
                        ->sortBy('layer_order')
                        ->values()
                        ->map(fn (Layer $layer): array => [
                            'layer_order' => $layer->layer_order,
                            'thickness' => $this->asFloat($layer->thickness),
                            'width' => $this->asFloat($layer->width),
                            'angle' => $this->asFloat($layer->angle),
                        ])
                        ->all(),
                ])
                ->all(),
        ];
    }

    private function asFloat(mixed $value): float
    {
        return round((float) $value, 2);
    }
}
