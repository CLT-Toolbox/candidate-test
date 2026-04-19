<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_suppliers(): void
    {
        $this->get(route('suppliers.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_and_view_supplier(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('suppliers.store'), [
                'name' => 'ACME Timber',
                'code' => 'ACME',
                'notes' => 'Test',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('suppliers', [
            'name' => 'ACME Timber',
            'code' => 'ACME',
        ]);

        $supplier = Supplier::where('name', 'ACME Timber')->first();
        $this->assertNotNull($supplier);

        $this->actingAs($user)
            ->get(route('suppliers.show', $supplier))
            ->assertOk();
    }
}
