<?php

namespace Tests\Feature;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SupplierImportExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_contains_nested_supplier_structure(): void
    {
        $this->actingAs(User::factory()->create());

        $supplier = Supplier::factory()->create(['name' => 'Export Supplier']);
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'L1']);
        Layer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 120,
            'angle' => 0,
        ]);

        $response = $this->get(route('suppliers.export', $supplier));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $response->assertJsonPath('supplier.name', 'Export Supplier');
        $response->assertJsonPath('layups.0.name', 'L1');
        $response->assertJsonPath('layups.0.layers.0.layer_order', 1);
    }

    public function test_import_overwrite_strategy_updates_conflicting_layer(): void
    {
        $this->actingAs(User::factory()->create());

        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'Base Layup']);
        Layer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 100,
            'angle' => 0,
        ]);

        $payload = [
            'layups' => [
                [
                    'name' => 'Base Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 14,
                            'width' => 110,
                            'angle' => 15,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post(route('suppliers.import', $supplier), [
            'strategy' => SupplierImportExportService::STRATEGY_OVERWRITE,
            'payload' => json_encode($payload),
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));

        $this->assertDatabaseHas('layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 14,
            'width' => 110,
            'angle' => 15,
        ]);
    }

    public function test_import_reject_stores_conflicts_for_matching_supplier_only(): void
    {
        $this->actingAs(User::factory()->create());

        $supplierA = Supplier::factory()->create();
        $supplierB = Supplier::factory()->create();

        $layupA = Layup::factory()->create(['supplier_id' => $supplierA->id, 'name' => 'Base Layup']);
        Layer::factory()->create([
            'layup_id' => $layupA->id,
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 100,
            'angle' => 0,
        ]);

        $payload = [
            'layups' => [
                [
                    'name' => 'Base Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 14,
                            'width' => 110,
                            'angle' => 15,
                        ],
                    ],
                ],
            ],
        ];

        $importResponse = $this->post(route('suppliers.import', $supplierA), [
            'strategy' => SupplierImportExportService::STRATEGY_REJECT,
            'payload' => json_encode($payload),
        ]);

        $importResponse->assertRedirect(route('suppliers.conflicts', $supplierA));

        $wrongSupplierResponse = $this->get(route('suppliers.conflicts', $supplierB));
        $wrongSupplierResponse->assertRedirect(route('suppliers.show', $supplierB));
    }

    public function test_import_invalid_json_returns_validation_error(): void
    {
        $this->actingAs(User::factory()->create());
        $supplier = Supplier::factory()->create();

        $response = $this->from(route('suppliers.show', $supplier))->post(route('suppliers.import', $supplier), [
            'strategy' => SupplierImportExportService::STRATEGY_OVERWRITE,
            'payload' => '{invalid-json}',
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));
        $response->assertSessionHasErrors(['payload']);
    }
}
