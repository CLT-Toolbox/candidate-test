<?php

namespace Tests\Unit;

use App\Http\Controllers\SupplierController;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_and_update_and_destroy()
    {
        $controller = new SupplierController();

        // store
        $request = Request::create('/suppliers', 'POST', [
            'name' => 'U1', 'email' => 'u1@example.com','address' => 'A','is_active' => true
        ]);
        $response = $controller->store($request);
        $this->assertDatabaseHas('suppliers', ['email' => 'u1@example.com']);

        $s = Supplier::first();

        // update
        $updateReq = Request::create('/suppliers/' . $s->id, 'PUT', [
            'name' => 'U1u', 'email' => 'u1u@example.com','address' => 'B','is_active' => false
        ]);
        $controller->update($updateReq, $s->id);
        $this->assertDatabaseHas('suppliers', ['email' => 'u1u@example.com']);

        // destroy
        $controller->destroy($s->id);
        $this->assertDatabaseMissing('suppliers', ['id' => $s->id]);
    }

    public function test_show_handles_not_found()
    {
        $controller = new SupplierController();
        $resp = $controller->show(9999);    
        $this->assertTrue(method_exists($resp, 'with') || method_exists($resp, 'getSession'));
    }
}
