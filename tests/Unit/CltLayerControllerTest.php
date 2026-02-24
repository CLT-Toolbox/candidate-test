<?php

namespace Tests\Unit;

use App\Http\Controllers\CltLayerController;
use App\Models\Supplier;
use App\Models\CltLayups;
use App\Models\CltLayers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_and_destroy()
    {
        $controller = new CltLayerController();

        $s = Supplier::create(['name' => 'S','email' => 's@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LU','supplier_id' => $s->id, 'status' => 1]);

        $req = Request::create('/clt-layups/' . $layup->id . '/layers', 'POST', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 1.5,
            'width' => 10,
            'angle' => 45,
        ]);
        $controller->store($req);
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'layer_order' => 1]);

        $layer = CltLayers::first();

        $controller->destroy($layer->id);
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }

    public function test_batch_update_deleted_only()
    {
        $controller = new CltLayerController();

        $s = Supplier::create(['name' => 'S','email' => 's@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LUd','supplier_id' => $s->id, 'status' => 1]);

        $a = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);
        $b = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 2.0, 'width' => 6.0, 'angle' => 20]);

        $changes = ['deleted' => [$b->id], 'created' => [], 'updated' => [], 'reordered' => []];
        $req = Request::create('/clt-layups/' . $layup->id . '/batch-update', 'POST', ['changes' => json_encode($changes)]);
        $controller->update($req, $layup->id);

        $this->assertDatabaseMissing('clt_layers', ['id' => $b->id]);
        $this->assertDatabaseHas('clt_layers', ['id' => $a->id]);
    }

    public function test_batch_update_created_only()
    {
        $controller = new CltLayerController();

        $s = Supplier::create(['name' => 'S','email' => 's2@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LUc','supplier_id' => $s->id, 'status' => 1]);

        $changes = ['deleted' => [], 'created' => [
            ['thickness' => 3.0, 'width' => 7.0, 'angle' => 30],
            ['thickness' => 4.0, 'width' => 8.0, 'angle' => 40],
        ], 'updated' => [], 'reordered' => []];

        $req = Request::create('/clt-layups/' . $layup->id . '/batch-update', 'POST', ['changes' => json_encode($changes)]);
        $controller->update($req, $layup->id);

        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'angle' => 30]);
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'angle' => 40]);
    }

    public function test_batch_update_updated_only()
    {
        $controller = new CltLayerController();

        $s = Supplier::create(['name' => 'S','email' => 's3@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LUu','supplier_id' => $s->id, 'status' => 1]);

        $layer = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1.0, 'width' => 5.0, 'angle' => 10]);

        $changes = ['deleted' => [], 'created' => [], 'updated' => [$layer->id => ['thickness' => 1.9, 'width' => 5.5]], 'reordered' => []];
        $req = Request::create('/clt-layups/' . $layup->id . '/batch-update', 'POST', ['changes' => json_encode($changes)]);
        $controller->update($req, $layup->id);

        $this->assertDatabaseHas('clt_layers', ['id' => $layer->id, 'thickness' => 1.9, 'width' => 5.5]);
    }

    public function test_batch_update_reordered_only()
    {
        $controller = new CltLayerController();

        $s = Supplier::create(['name' => 'S','email' => 's4@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LUr','supplier_id' => $s->id, 'status' => 1]);

        $l1 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1, 'width' => 5, 'angle' => 10]);
        $l2 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 2, 'width' => 6, 'angle' => 20]);

        $changes = ['deleted' => [], 'created' => [], 'updated' => [], 'reordered' => [
            ['id' => $l1->id, 'layer_order' => 2],
            ['id' => $l2->id, 'layer_order' => 1],
        ]];

        $req = Request::create('/clt-layups/' . $layup->id . '/batch-update', 'POST', ['changes' => json_encode($changes)]);
        $controller->update($req, $layup->id);

        $this->assertDatabaseHas('clt_layers', ['id' => $l1->id, 'layer_order' => 2]);
        $this->assertDatabaseHas('clt_layers', ['id' => $l2->id, 'layer_order' => 1]);
    }

    public function test_batch_update_all_combined()
    {
        $controller = new CltLayerController();

        $s = Supplier::create(['name' => 'S','email' => 's5@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LUall','supplier_id' => $s->id, 'status' => 1]);

        $l1 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 1, 'width' => 5, 'angle' => 10]);
        $l2 = CltLayers::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 2, 'width' => 6, 'angle' => 20]);

        $changes = [
            'deleted' => [$l2->id],
            'created' => [['thickness' => 3.3, 'width' => 7.7, 'angle' => 33]],
            'updated' => [$l1->id => ['thickness' => 1.1]],
            'reordered' => [
                ['id' => $l1->id, 'layer_order' => 2],
            ],
        ];

        $req = Request::create('/clt-layups/' . $layup->id . '/batch-update', 'POST', ['changes' => json_encode($changes)]);
        $controller->update($req, $layup->id);

        // deleted l2
        $this->assertDatabaseMissing('clt_layers', ['id' => $l2->id]);
        // updated l1
        $this->assertDatabaseHas('clt_layers', ['id' => $l1->id, 'thickness' => 1.1]);
        // created new layer exists
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'angle' => 33]);
    }
}
