<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\CltLayups;
use App\Models\CltLayers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_layup_detail_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);

        $response = $this->actingAs($user)->get('/clt-layups/' . $layup->id);

        $response->assertOk();
    }

    public function test_store_validates_input()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);

        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/layers', []);
        $response->assertSessionHasErrors();
    }

    public function test_store_creates_new_layer()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);

        $payload = [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 1.5,
            'width' => 10.0,
            'angle' => 45,
        ];

        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/layers', $payload);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'layer_order' => 1]);
    }

    public function test_batch_update_can_create_layers()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);

        $changes = [
            'created' => [['thickness' => 3.0, 'width' => 7.0, 'angle' => 30]],
        ];

        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/batch-update', ['changes' => json_encode($changes)]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'thickness' => 3.0, 'angle' => 30]);
    }

    public function test_batch_update_can_delete_layers()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);
        $l1 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);

        $changes = [
            'deleted' => [$l1->id],
        ];

        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/batch-update', ['changes' => json_encode($changes)]);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('clt_layers', ['id' => $l1->id]);
    }

    public function test_batch_update_can_update_layers()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);
        $l1 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);

        $changes = [
            'updated' => [
                $l1->id => ['thickness' => 1.2, 'width' => 5.5]
            ],
        ];

        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/batch-update', ['changes' => json_encode($changes)]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layers', ['id' => $l1->id, 'thickness' => 1.2]);
    }

    public function test_batch_update_can_reorder_layers()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);
        $l1 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);

        $changes = [
            'reordered' => [
                ['id' => $l1->id, 'layer_order' => 3],
            ],
        ];

        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/batch-update', ['changes' => json_encode($changes)]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layers', ['id' => $l1->id, 'layer_order' => 3]);
    }

    public function test_destroy_removes_layer()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay', 'supplier_id' => $s->id, 'status' => 1]);
        $layer = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 5, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);

        $response = $this->actingAs($user)->delete('/clt-layers/' . $layer->id);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }

    public function test_destroy_returns_error_when_layer_not_found()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/clt-layers/9999');
        $response->assertSessionHas('error');
    }
}
