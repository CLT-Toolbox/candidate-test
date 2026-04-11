<?php

namespace Tests\Unit\Services;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Services\Suppliers\SupplierImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_prepare_import_detects_field_level_layer_conflicts(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Acoustic Panel']);
        Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 32,
            'width' => 110,
            'angle' => 0,
        ]);

        /** @var SupplierImportService $service */
        $service = app(SupplierImportService::class);

        $prepared = $service->prepareImport($supplier, [
            'supplier' => ['name' => 'External Source'],
            'layups' => [
                [
                    'name' => 'Acoustic Panel',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 38, 'width' => 110, 'angle' => 45],
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $prepared['conflicts']);
        $this->assertSame('Acoustic Panel', $prepared['conflicts'][0]['layup_name']);
        $this->assertSame(['thickness', 'angle'], array_keys($prepared['conflicts'][0]['differences']));
    }
}
