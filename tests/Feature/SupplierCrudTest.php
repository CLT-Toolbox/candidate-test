<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Layup;
use App\Models\Layer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_suppliers_list()
    {
        $suppliers = Supplier::factory(3)->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.index'));

        $response->assertStatus(200);
        foreach ($suppliers as $supplier) {
            $response->assertSee($supplier->name);
        }
    }

    public function test_user_can_create_supplier()
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => 'Test Supplier',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Test Supplier',
        ]);
    }

    public function test_user_cannot_create_duplicate_supplier()
    {
        Supplier::create(['name' => 'Test Supplier']);

        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => 'Test Supplier',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_can_view_supplier_details()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create();
        $layer = Layer::factory()->for($layup)->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.show', $supplier));

        $response->assertStatus(200);
        $response->assertSee($supplier->name);
        $response->assertSee($layup->name);
    }

    public function test_user_can_update_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->patch(
            route('suppliers.update', $supplier),
            ['name' => 'Updated Supplier']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier',
        ]);
    }

    public function test_user_can_delete_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect();
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
