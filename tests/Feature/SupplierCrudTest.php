<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_suppliers()
    {
        $user = User::factory()->create();
        Supplier::create(['name' => 'S1','email' => 's1@example.com','address' => 'Addr','is_active' => true]);

        $response = $this->actingAs($user)->get('/suppliers');

        $response->assertOk();
    }

    public function test_store_creates_supplier_and_validates()
    {
        $user = User::factory()->create();

        // Missing fields
        $response = $this->actingAs($user)->post('/suppliers', []);
        $response->assertSessionHasErrors();

        // Correct payload 
        $payload = ['name' => 'New Supplier','email' => 'new@example.com','address' => 'Addr','is_active' => true];
        $response = $this->actingAs($user)->post('/suppliers', $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', ['email' => 'new@example.com']);
    }

    public function test_update_updates_supplier_and_handles_not_found()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'S','email' => 's@example.com','address' => 'A','is_active' => true]);

        // update with invalid payload
        $response = $this->actingAs($user)->put('/suppliers/' . $s->id, ['name' => '']);
        $response->assertSessionHasErrors();

        // successful update
        $payload = ['name' => 'S Updated','email' => 'supdated@example.com','address' => 'B','is_active' => false];
        $response = $this->actingAs($user)->put('/suppliers/' . $s->id, $payload);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', ['email' => 'supdated@example.com', 'name' => 'S Updated']);

        // not found 
        $response = $this->actingAs($user)->put('/suppliers/9999', $payload);
        $response->assertSessionHas('error');
    }

    public function test_show_and_destroy()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'S2','email' => 's2@example.com','address' => 'A','is_active' => true]);

        $response = $this->actingAs($user)->get('/suppliers/' . $s->id);
        $response->assertOk();

        $response = $this->actingAs($user)->delete('/suppliers/' . $s->id);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('suppliers', ['id' => $s->id]);

        // delete non-existent
        $response = $this->actingAs($user)->delete('/suppliers/9999');
        $response->assertSessionHas('error');
    }
}
