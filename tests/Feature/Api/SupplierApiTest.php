<?php

namespace Tests\Feature\Api;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_crud_flow(): void
    {
        $createResponse = $this->postJson('/api/suppliers', [
            'name' => 'Supplier Alpha',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Supplier Alpha');

        $supplierId = $createResponse->json('data.id');

        Supplier::factory()->create(['name' => 'Supplier Beta']);

        $this->getJson('/api/suppliers?name=Supplier')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.data.0.name', 'Supplier Alpha');

        $this->getJson("/api/suppliers/{$supplierId}")
            ->assertOk()
            ->assertJsonPath('data.id', $supplierId)
            ->assertJsonPath('data.name', 'Supplier Alpha');

        $this->putJson("/api/suppliers/{$supplierId}", [
            'name' => 'Supplier Alpha Updated',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Supplier Alpha Updated');

        $this->deleteJson("/api/suppliers/{$supplierId}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplierId,
        ]);
    }
}
