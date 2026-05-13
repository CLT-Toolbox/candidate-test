<?php

namespace Tests\Feature;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NestedLayupLayerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_manage_nested_layup_and_layer(): void
    {
        $this->actingAs(User::factory()->create());
        $supplier = Supplier::factory()->create();

        $storeLayupResponse = $this->post(route('suppliers.layups.store', $supplier), [
            'name' => 'L-100',
            'description' => 'Test layup',
        ]);

        $layup = Layup::query()->firstOrFail();
        $storeLayupResponse->assertRedirect(route('suppliers.layups.show', [$supplier, $layup]));

        $storeLayerResponse = $this->post(route('suppliers.layups.layers.store', [$supplier, $layup]), [
            'layer_order' => 1,
            'thickness' => 12.5,
            'width' => 140,
            'angle' => 45,
        ]);

        $layer = Layer::query()->firstOrFail();
        $storeLayerResponse->assertRedirect(route('suppliers.layups.layers.show', [$supplier, $layup, $layer]));

        $this->assertDatabaseHas('layups', [
            'supplier_id' => $supplier->id,
            'name' => 'L-100',
        ]);

        $this->assertDatabaseHas('layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 12.5,
        ]);
    }
}
