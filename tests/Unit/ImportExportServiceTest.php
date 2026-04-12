<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Services\ImportExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportExportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ImportExportService $service;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ImportExportService::class);
        $this->supplier = Supplier::factory()->create();
    }

    public function test_export_includes_all_layups_and_layers(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
        CltLayer::factory(3)->create(['layup_id' => $layup->id]);

        $export = $this->service->export($this->supplier);

        $this->assertArrayHasKey('supplier', $export);
        $this->assertArrayHasKey('layups', $export);
        $this->assertCount(1, $export['layups']);
        $this->assertCount(3, $export['layups'][0]['layers']);
    }

    public function test_detect_conflicts_identifies_differing_layers(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Test']);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 40,
            'width' => 1200,
            'angle' => 0,
        ]);

        $importData = [
            'layups' => [
                [
                    'name' => 'Test',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 45, 'width' => 1200, 'angle' => 0],
                    ],
                ],
            ],
        ];

        $conflicts = $this->service->detectConflicts($this->supplier, $importData);

        $this->assertCount(1, $conflicts);
        $this->assertContains('thickness', $conflicts[0]['layers'][0]['diff_fields']);
    }

    public function test_detect_conflicts_no_conflicts_for_new_layup(): void
    {
        $importData = [
            'layups' => [
                [
                    'name' => 'New Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 40, 'width' => 1200, 'angle' => 0],
                    ],
                ],
            ],
        ];

        $conflicts = $this->service->detectConflicts($this->supplier, $importData);

        $this->assertEmpty($conflicts);
    }

    public function test_detect_conflicts_with_multiple_diff_fields(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Multi']);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 40,
            'width' => 1200,
            'angle' => 0,
        ]);

        $importData = [
            'layups' => [
                [
                    'name' => 'Multi',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 50, 'width' => 1300, 'angle' => 90],
                    ],
                ],
            ],
        ];

        $conflicts = $this->service->detectConflicts($this->supplier, $importData);

        $this->assertCount(3, $conflicts[0]['layers'][0]['diff_fields']);
        $this->assertContains('thickness', $conflicts[0]['layers'][0]['diff_fields']);
        $this->assertContains('width', $conflicts[0]['layers'][0]['diff_fields']);
        $this->assertContains('angle', $conflicts[0]['layers'][0]['diff_fields']);
    }
}