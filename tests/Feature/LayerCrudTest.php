<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Layup;
use App\Models\Layer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_layer()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create();

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$supplier, $layup]),
            [
                'layer_order' => 1,
                'thickness' => 2.5,
                'width' => 100.0,
                'angle' => 45.0,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
        ]);
    }

    public function test_user_cannot_create_duplicate_layer_order()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create();
        Layer::factory()->for($layup)->create(['layer_order' => 1]);

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$supplier, $layup]),
            [
                'layer_order' => 1,
                'thickness' => 2.5,
                'width' => 100.0,
                'angle' => 45.0,
            ]
        );

        $response->assertSessionHasErrors('layer_order');
    }

    public function test_user_can_update_layer()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create();
        $layer = Layer::factory()->for($layup)->create();

        $response = $this->actingAs($this->user)->patch(
            route('suppliers.layups.layers.update', [$supplier, $layup, $layer]),
            [
                'layer_order' => $layer->layer_order,
                'thickness' => 3.5,
                'width' => 150.0,
                'angle' => 90.0,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer->id,
            'thickness' => '3.5000',
        ]);
    }

    public function test_user_can_delete_layer()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create();
        $layer = Layer::factory()->for($layup)->create();

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer])
        );

        $response->assertRedirect();
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }
}
