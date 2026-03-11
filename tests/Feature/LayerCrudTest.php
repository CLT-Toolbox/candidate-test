<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\Layup;
use App\Models\Layer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LayerCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * Fitur: Create Layer
     * Skenario: User bisa membuat layer baru dengan data yang valid
     */
    public function can_create_layer()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);

        $data = [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 10.5,
            'width' => 100.0,
            'angle' => 0,
        ];

        $response = $this->postJson('/api/layers', $data);

        $response->assertStatus(201);
        $response->assertJsonPath('layer_order', 1);

        $this->assertDatabaseHas('layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 10.5,
        ]);
    }

    /**
     * @test
     * Fitur: Read Layers (Index)
     * Skenario: User bisa melihat daftar semua layer
     */
    public function can_read_layers()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);

        Layer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0]);
        Layer::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 15, 'width' => 100, 'angle' => 90]);
        Layer::create(['layup_id' => $layup->id, 'layer_order' => 3, 'thickness' => 10, 'width' => 100, 'angle' => 0]);

        $response = $this->getJson('/api/layers');

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJsonFragment(['layer_order' => 1]);
        $response->assertJsonFragment(['layer_order' => 2]);
        $response->assertJsonFragment(['layer_order' => 3]);
    }

    /**
     * @test
     * Fitur: Read Single Layer (Show)
     * Skenario: User bisa melihat detail 1 layer berdasarkan ID
     */
    public function can_read_single_layer()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);
        $layer = Layer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0]);

        $response = $this->getJson('/api/layers/' . $layer->id);

        $response->assertStatus(200);
        $response->assertJsonPath('layer_order', 1);
        $response->assertJsonPath('id', $layer->id);
    }

    /**
     * @test
     * Fitur: Update Layer
     * Skenario: User bisa mengubah data layer yang sudah ada
     */
    public function can_update_layer()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);
        $layer = Layer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0]);

        $data = [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 15.5,
            'width' => 120.0,
            'angle' => 45,
        ];

        $response = $this->putJson('/api/layers/' . $layer->id, $data);

        $response->assertStatus(200);
        $response->assertJsonPath('thickness', 15.5);

        $this->assertDatabaseHas('layers', [
            'id' => $layer->id,
            'thickness' => 15.5,
            'width' => 120.0,
        ]);
    }

    /**
     * @test
     * Fitur: Delete Layer
     * Skenario: User bisa menghapus layer
     */
    public function can_delete_layer()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);
        $layer = Layer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0]);

        $response = $this->deleteJson('/api/layers/' . $layer->id);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Layer deleted successfully.');

        $this->assertDatabaseMissing('layers', [
            'id' => $layer->id,
        ]);
    }

    /**
     * @test
     * Fitur: Validation - Layer Order is Required
     * Skenario: User tidak bisa create layer tanpa layer_order
     */
    public function layer_order_is_required()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);

        $data = [
            'layup_id' => $layup->id,
            'layer_order' => '',
            'thickness' => 10,
            'width' => 100,
            'angle' => 0,
        ];

        $response = $this->postJson('/api/layers', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('layer_order');
    }

    /**
     * @test
     * Fitur: Validation - Duplicate Layer Order
     * Skenario: User tidak bisa create layer dengan layer_order yang sama di layup yang sama
     */
    public function cannot_create_duplicate_layer_order()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);

        Layer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0]);

        $data = [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 15,
            'width' => 100,
            'angle' => 90,
        ];

        $response = $this->postJson('/api/layers', $data);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Layer order already exists in this layup.');
    }

    /**
     * @test
     * Fitur: Export Supplier with Layups and Layers
     * Skenario: User bisa export supplier beserta semua layups dan layers
     */
    public function can_export_supplier_with_layups_and_layers()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);
        Layer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0]);
        Layer::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 15, 'width' => 100, 'angle' => 90]);

        $response = $this->getJson('/api/suppliers/' . $supplier->id . '/export');

        $response->assertStatus(200);
        $response->assertJsonPath('supplier.name', 'Test Supplier');
        $response->assertJsonFragment(['name' => 'CLT Layup A']);
    }

    /**
     * @test
     * Fitur: Import Supplier Data with Skip Strategy
     * Skenario: User bisa import data supplier dengan strategy skip
     */
    public function can_import_supplier_data_with_skip_strategy()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'CLT Layup A']);
        Layer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0]);

        $importData = [
            'layups' => [
                [
                    'name' => 'CLT Layup A',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 15,
                            'width' => 120,
                            'angle' => 45,
                        ],
                        [
                            'layer_order' => 2,
                            'thickness' => 20,
                            'width' => 100,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/suppliers/' . $supplier->id . '/import?strategy=skip', [
            'file' => json_encode($importData),
        ]);

        $response->assertStatus(422);
    }
}
