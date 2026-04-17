<?php

namespace Tests\Feature\Api;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayupApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_layup_crud_flow(): void
    {
        $supplier = Supplier::factory()->create();

        $createResponse = $this->postJson("/api/suppliers/{$supplier->id}/layups", [
            'name' => 'Layup A',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Layup A')
            ->assertJsonPath('data.supplier_id', $supplier->id);

        $layupId = $createResponse->json('data.id');

        Layup::factory()->for($supplier)->create(['name' => 'Layup B']);

        $this->getJson("/api/suppliers/{$supplier->id}/layups")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.data');

        $this->getJson("/api/layups/{$layupId}")
            ->assertOk()
            ->assertJsonPath('data.id', $layupId)
            ->assertJsonPath('data.name', 'Layup A');

        $this->putJson("/api/layups/{$layupId}", [
            'name' => 'Layup A Updated',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Layup A Updated');

        $this->deleteJson("/api/layups/{$layupId}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('layups', [
            'id' => $layupId,
        ]);
    }
}
