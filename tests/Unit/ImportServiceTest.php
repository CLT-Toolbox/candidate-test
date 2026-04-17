<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Models\Layup;
use App\Models\Layer;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ImportService();
    }

    public function test_import_new_supplier_data()
    {
        $data = [
            'name' => 'New Supplier',
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 2.5,
                            'width' => 100,
                            'angle' => 45,
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->service->import($data);

        $this->assertDatabaseHas('suppliers', ['name' => 'New Supplier']);
        $this->assertDatabaseHas('clt_layups', ['name' => 'Layup 1']);
        $this->assertDatabaseHas('clt_layers', ['layer_order' => 1]);
    }

    public function test_import_with_overwrite_strategy()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Layup 1']);
        Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 2.5,
            'width' => 100,
            'angle' => 45,
        ]);

        $incomingData = [
            'name' => $supplier->name,
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 5.0,
                            'width' => 200,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ];

        $this->service->setConflictStrategy(ImportService::STRATEGY_OVERWRITE);
        $result = $this->service->import($incomingData, $supplier);

        $this->assertDatabaseHas('clt_layers', [
            'layer_order' => 1,
            'thickness' => '5.0000',
            'width' => '200.0000',
            'angle' => '90.0000',
        ]);
    }

    public function test_import_with_skip_strategy()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Layup 1']);
        $originalLayer = Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 2.5,
        ]);

        $incomingData = [
            'name' => $supplier->name,
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 5.0,
                            'width' => 200,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ];

        $this->service->setConflictStrategy(ImportService::STRATEGY_SKIP);
        $result = $this->service->import($incomingData, $supplier);

        // Original data should remain unchanged
        $this->assertDatabaseHas('clt_layers', [
            'id' => $originalLayer->id,
            'thickness' => '2.5000',
        ]);
    }

    public function test_import_with_duplicate_strategy()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Layup 1']);
        Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 2.5,
        ]);

        $incomingData = [
            'name' => $supplier->name,
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 5.0,
                            'width' => 200,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ];

        $this->service->setConflictStrategy(ImportService::STRATEGY_DUPLICATE);
        $result = $this->service->import($incomingData, $supplier);

        // New layup should be created with suffix
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Layup 1 (imported)',
        ]);
    }

    public function test_import_creates_new_layers_in_existing_layup()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Layup 1']);
        Layer::factory()->for($layup)->create(['layer_order' => 1]);

        $incomingData = [
            'name' => $supplier->name,
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        [
                            'layer_order' => 2,
                            'thickness' => 3.0,
                            'width' => 150,
                            'angle' => 60,
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->service->import($incomingData, $supplier);

        // New layer should be created
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 2,
        ]);
    }
}
