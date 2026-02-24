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

    public function test_store_creates_layup_and_validates()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS','email' => 'ls@example.com','address' => 'A','is_active' => true]);

        // Missing -> validation
        $response = $this->actingAs($user)->post('/clt-layups', []);
        $response->assertSessionHasErrors();

        $payload = ['name' => 'Layup1', 'supplier_id' => $s->id, 'status' => 1, 'species_grade' => 'A'];
        $response = $this->actingAs($user)->post('/clt-layups', $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layups', ['name' => 'Layup1', 'supplier_id' => $s->id]);
    }

    public function test_update_and_not_found()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS2','email' => 'ls2@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'L','supplier_id' => $s->id, 'status' => 1]);

        // invalid update
        $response = $this->actingAs($user)->put('/clt-layups/' . $layup->id, ['name' => '']);
        $response->assertSessionHasErrors();

        $payload = ['name' => 'L Updated', 'supplier_id' => $s->id, 'status' => 2, 'species_grade' => 'B'];
        $response = $this->actingAs($user)->put('/clt-layups/' . $layup->id, $payload);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clt_layups', ['id' => $layup->id, 'name' => 'L Updated']);

        $response = $this->actingAs($user)->put('/clt-layups/9999', $payload);
        $response->assertSessionHasErrors();
    }

    public function test_show_and_destroy()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'LS3','email' => 'ls3@example.com','address' => 'A','is_active' => true]);
        $layup = CltLayups::create(['name' => 'LX','supplier_id' => $s->id, 'status' => 1]);

        $response = $this->actingAs($user)->get('/clt-layups/' . $layup->id);
        $response->assertOk();

        $response = $this->actingAs($user)->delete('/clt-layups/' . $layup->id);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);

        $response = $this->actingAs($user)->delete('/clt-layups/9999');
        $response->assertSessionHas('error');
    }
}
