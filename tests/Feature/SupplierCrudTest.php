<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_crud_supplier(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $createResponse = $this->post(route('suppliers.store'), [
            'name' => 'PT Alpha Wood',
            'code' => 'ALPHA-001',
            'address' => 'Bandung',
        ]);

        $supplier = Supplier::query()->firstOrFail();

        $createResponse
            ->assertRedirect(route('suppliers.show', $supplier));

        $this->assertDatabaseHas('suppliers', [
            'name' => 'PT Alpha Wood',
            'code' => 'ALPHA-001',
        ]);

        $updateResponse = $this->put(route('suppliers.update', $supplier), [
            'name' => 'PT Alpha Wood Updated',
            'code' => 'ALPHA-001',
            'address' => 'Jakarta',
        ]);

        $updateResponse->assertRedirect(route('suppliers.show', $supplier));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'PT Alpha Wood Updated',
            'address' => 'Jakarta',
        ]);

        $deleteResponse = $this->delete(route('suppliers.destroy', $supplier));

        $deleteResponse->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
