<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_suppliers_index(): void
    {
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('dashboard.suppliers.index'));
        
        $response->assertStatus(200);
    }

    public function test_can_create_supplier(): void
    {
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('dashboard.suppliers.store'), [
            'name' => 'Test Supplier'
        ]);
        
        $response->assertRedirect(route('dashboard.suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Test Supplier']);
    }

    public function test_can_update_supplier(): void
    {
        $user = \App\Models\User::factory()->create();
        $supplier = Supplier::create(['name' => 'Old Name']);
        
        $response = $this->actingAs($user)->put(route('dashboard.suppliers.update', $supplier), [
            'name' => 'New Name'
        ]);
        
        $response->assertRedirect(route('dashboard.suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'New Name']);
    }

    public function test_can_delete_supplier(): void
    {
        $user = \App\Models\User::factory()->create();
        $supplier = Supplier::create(['name' => 'To Delete']);
        
        $response = $this->actingAs($user)->delete(route('dashboard.suppliers.destroy', $supplier));
        
        $response->assertRedirect(route('dashboard.suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_supplier_has_layups_relationship(): void
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $supplier->layups()->create(['name' => 'Layup 1']);
        
        $this->assertEquals(1, $supplier->layups->count());
    }
}