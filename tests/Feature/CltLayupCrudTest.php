<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\CltLayups;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_layup_detail_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS3', 'email' => 'ls3@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'LX', 'supplier_id' => $s->id, 'status' => 1]);

        $response = $this->actingAs($user)->get('/clt-layups/' . $layup->id);
        $response->assertOk();
    }

    public function test_store_validates_input()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/clt-layups', []);
        $response->assertSessionHasErrors();
    }

    public function test_store_creates_new_layup()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS', 'email' => 'ls@example.com', 'address' => 'A', 'is_active' => true]);

        $payload = ['name' => 'Layup1', 'supplier_id' => $s->id, 'status' => 1, 'species_grade' => 'A'];
        $response = $this->actingAs($user)->post('/clt-layups', $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layups', ['name' => 'Layup1', 'supplier_id' => $s->id]);
    }

    public function test_update_validates_input()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS2', 'email' => 'ls2@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'L', 'supplier_id' => $s->id, 'status' => 1]);

        $response = $this->actingAs($user)->put('/clt-layups/' . $layup->id, ['name' => '']);
        $response->assertSessionHasErrors();
    }

    public function test_update_updates_layup()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS2', 'email' => 'ls2@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'L', 'supplier_id' => $s->id, 'status' => 1]);

        $payload = ['name' => 'L Updated', 'supplier_id' => $s->id, 'status' => 2, 'species_grade' => 'B'];
        $response = $this->actingAs($user)->put('/clt-layups/' . $layup->id, $payload);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layups', ['id' => $layup->id, 'name' => 'L Updated']);
    }

    public function test_update_returns_error_when_layup_not_found()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS2', 'email' => 'ls2@example.com', 'address' => 'A', 'is_active' => true]);
        
        $payload = ['name' => 'L Updated', 'supplier_id' => $s->id, 'status' => 2, 'species_grade' => 'B'];
        $response = $this->actingAs($user)->put('/clt-layups/9999', $payload);
        $response->assertSessionHas('error');
    }

    public function test_destroy_removes_layup()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS3', 'email' => 'ls3@example.com', 'address' => 'A', 'is_active' => true]);
        $layup = CltLayups::create(['name' => 'LX', 'supplier_id' => $s->id, 'status' => 1]);

        $response = $this->actingAs($user)->delete('/clt-layups/' . $layup->id);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }

    public function test_destroy_returns_error_when_layup_not_found()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/clt-layups/9999');
        $response->assertSessionHas('error');
    }
}
