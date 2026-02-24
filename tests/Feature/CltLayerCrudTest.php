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

    public function test_store_layer_creates_layer_and_validates()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS','email' => 'lsx@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'Lay','supplier_id' => $s->id, 'status' => 1]);

        // missing fields
        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/layers', []);
        $response->assertSessionHasErrors();

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

    public function test_batch_update_and_destroy()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LSB','email' => 'lsb@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LayB','supplier_id' => $s->id, 'status' => 1]);

        // create initial layers
        $l1 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);
        $l2 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 2.0, 'width' => 6.0, 'angle' => 20]);

        $changes = [
            'deleted' => [$l2->id],
            'created' => [['thickness' => 3.0, 'width' => 7.0, 'angle' => 30]],
            'updated' => [
                $l1->id => ['thickness' => 1.2, 'width' => 5.5]
            ],           
            'reordered' => [
                ['id' => $l1->id, 'layer_order' => 3],
            ],
        ];

        $response = $this->actingAs($user)->post('/clt-layups/' . $layup->id . '/batch-update', ['changes' => json_encode($changes)]);
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('clt_layers', ['id' => $l2->id]);
        $this->assertDatabaseHas('clt_layers', ['id' => $l1->id, 'thickness' => 1.2]);
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'angle' => 30]);

        // destroy
        $layer = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 5, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);
        $response = $this->actingAs($user)->delete('/clt-layers/' . $layer->id);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);

        // destroy not found
        $response = $this->actingAs($user)->delete('/clt-layers/9999');
        $response->assertSessionHas('error');
    }
}
