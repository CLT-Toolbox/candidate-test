<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Layup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayupCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_layup()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $supplier),
            ['name' => 'Test Layup']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Test Layup',
        ]);
    }

    public function test_user_cannot_create_duplicate_layup_under_same_supplier()
    {
        $supplier = Supplier::factory()->create();
        Layup::factory()->for($supplier)->create(['name' => 'Test Layup']);

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $supplier),
            ['name' => 'Test Layup']
        );

        $response->assertSessionHasErrors('name');
    }

    public function test_user_can_create_same_layup_name_under_different_supplier()
    {
        $supplier1 = Supplier::factory()->create();
        $supplier2 = Supplier::factory()->create();

        Layup::factory()->for($supplier1)->create(['name' => 'Test Layup']);

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $supplier2),
            ['name' => 'Test Layup']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier2->id,
            'name' => 'Test Layup',
        ]);
    }

    public function test_user_can_update_layup()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create();

        $response = $this->actingAs($this->user)->patch(
            route('suppliers.layups.update', [$supplier, $layup]),
            ['name' => 'Updated Layup']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('clt_layups', [
            'id' => $layup->id,
            'name' => 'Updated Layup',
        ]);
    }

    public function test_user_can_delete_layup()
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create();

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.destroy', [$supplier, $layup])
        );

        $response->assertRedirect();
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }
}
