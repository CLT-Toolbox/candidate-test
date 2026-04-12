<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    public function test_can_create_layup(): void
    {
        $data = [
            'name' => 'CLT-5-150-L',
            'layup_code' => 'L-204-A',
            'revision' => 'Rev 1',
            'status' => 'active',
            'species_grade' => 'Spruce / No. 2',
        ];

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $this->supplier),
            $data
        );

        $this->assertDatabaseHas('clt_layups', ['name' => 'CLT-5-150-L', 'supplier_id' => $this->supplier->id]);
        $response->assertSessionHas('success');
    }

    public function test_create_layup_requires_name_and_code(): void
    {
        $data = ['status' => 'active'];

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $this->supplier),
            $data
        );

        $response->assertSessionHasErrors(['name', 'layup_code']);
    }

    public function test_layup_code_must_be_unique(): void
    {
        CltLayup::factory()->create(['layup_code' => 'DUPLICATE-CODE']);

        $data = [
            'name' => 'Test Layup',
            'layup_code' => 'DUPLICATE-CODE',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $this->supplier),
            $data
        );

        $response->assertSessionHasErrors('layup_code');
    }

    public function test_can_view_layup_details(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $response = $this->actingAs($this->user)->get(
            route('suppliers.layups.show', [$this->supplier, $layup])
        );

        $response->assertStatus(200);
        $response->assertViewHas('layup');
        $response->assertViewIs('layups.index');
    }

    public function test_can_update_layup(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $data = [
            'name' => 'Updated Layup',
            'layup_code' => $layup->layup_code,
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->user)->patch(
            route('suppliers.layups.update', [$this->supplier, $layup]),
            $data
        );

        $this->assertDatabaseHas('clt_layups', ['id' => $layup->id, 'name' => 'Updated Layup']);
    }

    public function test_can_delete_layup(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.destroy', [$this->supplier, $layup])
        );

        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
        $response->assertSessionHas('success');
    }
}