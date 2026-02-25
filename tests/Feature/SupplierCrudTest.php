<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_supplier_page_can_be_rendered()
    {
        $user = User::factory()->create();
        Supplier::create(['name' => 'S1','email' => 's1@example.com','address' => 'Addr','is_active' => true]);

        $response = $this->actingAs($user)->get('/suppliers');

        $response->assertOk();
    }

    public function test_not_login_user_cannot_access_supplier_page()
    {
        $response = $this->get('/suppliers');
        $response->assertRedirect('/login');
    }

    public function test_not_login_user_cannot_access_supplier_detail_page()
    {
        $s = Supplier::create(['name' => 'S1','email' => 's1@example.com','address' => 'Addr','is_active' => true]);
        $response = $this->get('/suppliers/' . $s->id);
        $response->assertRedirect('/login');
    }


    public function test_supplier_show_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'S2','email' => 's2@example.com','address' => 'A','is_active' => true]);

        $response = $this->actingAs($user)->get('/suppliers/' . $s->id);
        $response->assertOk();
    }

    public function test_store_validates_input()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/suppliers', []);
        $response->assertSessionHasErrors();
    }

    public function test_store_creates_new_supplier()
    {
        $user = User::factory()->create();

        $payload = ['name' => 'New Supplier','email' => 'new@example.com','address' => 'Addr','is_active' => true];
        $response = $this->actingAs($user)->post('/suppliers', $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', ['email' => 'new@example.com']);
    }

    public function test_update_validates_input()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'S','email' => 's@example.com','address' => 'A','is_active' => true]);

        $response = $this->actingAs($user)->put('/suppliers/' . $s->id, ['name' => '']);
        $response->assertSessionHasErrors();
    }

    public function test_update_updates_supplier()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'S','email' => 's@example.com','address' => 'A','is_active' => true]);

        $payload = ['name' => 'S Updated','email' => 'supdated@example.com','address' => 'B','is_active' => false];
        $response = $this->actingAs($user)->put('/suppliers/' . $s->id, $payload);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', ['email' => 'supdated@example.com', 'name' => 'S Updated']);
    }

    public function test_update_returns_error_when_supplier_not_found()
    {
        $user = User::factory()->create();
        $payload = ['name' => 'S Updated','email' => 'supdated@example.com','address' => 'B','is_active' => false];
        $response = $this->actingAs($user)->put('/suppliers/9999', $payload);
        $response->assertSessionHas('error');
    }

    public function test_destroy_removes_supplier()
    {
        $user = User::factory()->create();
        $s = Supplier::create(['name' => 'S2','email' => 's2@example.com','address' => 'A','is_active' => true]);

        $response = $this->actingAs($user)->delete('/suppliers/' . $s->id);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('suppliers', ['id' => $s->id]);
    }

    public function test_destroy_returns_error_when_supplier_not_found()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/suppliers/9999');
        $response->assertSessionHas('error');
    }
}
