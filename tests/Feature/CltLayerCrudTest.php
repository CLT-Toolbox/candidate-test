<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;
    protected CltLayup $layup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
    }

    public function test_can_create_layer(): void
    {
        $data = [
            'layer_order' => 1,
            'thickness' => 40.5,
            'width' => 1200,
            'angle' => 0,
            'species_grade' => 'C24',
        ];

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            $data
        );

        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $this->layup->id,
            'layer_order' => 1,
            'thickness' => 40.50,
        ]);
        $response->assertSessionHas('success');
    }

    public function test_create_layer_requires_all_numeric_fields(): void
    {
        $data = [
            'layer_order' => 1,
            'thickness' => 'invalid',
        ];

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            $data
        );

        $response->assertSessionHasErrors(['thickness', 'width', 'angle']);
    }

    public function test_layer_order_must_be_positive(): void
    {
        $data = [
            'layer_order' => -1,
            'thickness' => 40,
            'width' => 1200,
            'angle' => 0,
        ];

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            $data
        );

        $response->assertSessionHasErrors('layer_order');
    }

    public function test_can_update_layer(): void
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $data = [
            'layer_order' => 2,
            'thickness' => 45.0,
            'width' => 1400,
            'angle' => 90,
        ];

        $response = $this->actingAs($this->user)->patch(
            route('suppliers.layups.layers.update', [$this->supplier, $this->layup, $layer]),
            $data
        );

        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer->id,
            'angle' => 90,
            'width' => 1400.00,
        ]);
    }

    public function test_can_delete_layer(): void
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.layers.destroy', [$this->supplier, $this->layup, $layer])
        );

        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
        $response->assertSessionHas('success');
    }
}