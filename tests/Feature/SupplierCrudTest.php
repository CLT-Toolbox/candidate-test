<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
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

    public function test_can_view_suppliers_list(): void
    {
        Supplier::factory(3)->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('suppliers');
        $response->assertViewIs('suppliers.index');
    }

    public function test_can_create_supplier(): void
    {
        $data = [
            'name' => 'Nordic CLT Supplier',
            'primary_contact' => 'engineer@nordic.ca',
            'location' => 'Montreal, QC',
            'material_certifications' => 'SPF No. 1/2',
            'last_audit_date' => '2026-04-10',
        ];

        $response = $this->actingAs($this->user)->post(route('suppliers.store'), $data);

        $this->assertDatabaseHas('suppliers', ['name' => 'Nordic CLT Supplier']);
        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('success');
    }

    public function test_create_supplier_requires_name(): void
    {
        $data = [
            'primary_contact' => 'test@example.com',
        ];

        $response = $this->actingAs($this->user)->post(route('suppliers.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    public function test_supplier_name_must_be_unique(): void
    {
        Supplier::factory()->create(['name' => 'Duplicate Supplier']);

        $data = ['name' => 'Duplicate Supplier'];

        $response = $this->actingAs($this->user)->post(route('suppliers.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_view_supplier_details(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.show', $supplier));

        $response->assertStatus(200);
        $response->assertViewHas('supplier');
        $response->assertViewIs('suppliers.show');
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Old Name']);

        $data = [
            'name' => 'Updated Name',
            'primary_contact' => 'updated@example.com',
            'location' => 'Toronto, ON',
        ];

        $response = $this->actingAs($this->user)->patch(route('suppliers.update', $supplier), $data);

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Updated Name']);
        $response->assertRedirect(route('suppliers.show', $supplier));
        $response->assertSessionHas('success');
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('suppliers.destroy', $supplier));

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('success');
    }

    public function test_unauthenticated_user_cannot_access_suppliers(): void
    {
        $response = $this->get(route('suppliers.index'));

        $response->assertRedirect(route('login'));
    }
}