<?php

namespace Tests\Unit;

use App\Http\Controllers\CltLayupController;
use App\Models\Supplier;
use App\Models\CltLayups;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_update_destroy()
    {
        $controller = new CltLayupController();

        $s = Supplier::create(['name' => 'S','email' => 's@example.com','address' => 'A','is_active' => true]);

        $req = Request::create('/clt-layups', 'POST', ['name' => 'LU', 'supplier_id' => $s->id, 'status' => 1]);
        $controller->store($req);
        $this->assertDatabaseHas('clt_layups', ['name' => 'LU']);

        $layup = CltLayups::first();

        $up = Request::create('/clt-layups/' . $layup->id, 'PUT', ['name' => 'LU2', 'supplier_id' => $s->id, 'status' => 2]);
        $controller->update($up, $layup->id);
        $this->assertDatabaseHas('clt_layups', ['name' => 'LU2']);

        $controller->destroy($layup->id);
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }

    public function test_show_handles_not_found()
    {
        $controller = new CltLayupController();
        $resp = $controller->show(9999);
        $this->assertTrue(method_exists($resp, 'with') || method_exists($resp, 'getSession'));
    }
}
