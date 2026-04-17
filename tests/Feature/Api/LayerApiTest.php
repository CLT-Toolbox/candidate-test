<?php

namespace Tests\Feature\Api;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_layer_crud_flow(): void
    {
        $layup = Layup::factory()->create();

        $createResponse = $this->postJson("/api/layups/{$layup->id}/layers", [
            'layer_order' => 1,
            'thickness' => 1.2,
            'width' => 10,
            'angle' => 45,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.layer_order', 1);

        $layerId = $createResponse->json('data.id');

        Layer::factory()->for($layup)->create([
            'layer_order' => 2,
            'thickness' => 2.5,
            'width' => 12,
            'angle' => 90,
        ]);

        $this->getJson("/api/layups/{$layup->id}/layers")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.data');

        $this->getJson("/api/layers/{$layerId}")
            ->assertOk()
            ->assertJsonPath('data.id', $layerId)
            ->assertJsonPath('data.width', 10);

        $this->putJson("/api/layers/{$layerId}", [
            'layer_order' => 1,
            'thickness' => 1.5,
            'width' => 15,
            'angle' => 30,
        ])
            ->assertOk()
            ->assertJsonPath('data.thickness', 1.5)
            ->assertJsonPath('data.width', 15);

        $this->deleteJson("/api/layers/{$layerId}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('layers', [
            'id' => $layerId,
        ]);
    }
}
