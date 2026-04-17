<?php

namespace Tests\Feature\Api;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_returns_full_nested_structure(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Supplier Export']);
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Layup A']);
        Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 1.5,
            'width' => 11,
            'angle' => 30,
        ]);

        $response = $this->getJson("/api/suppliers/{$supplier->id}/export");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $supplier->id)
            ->assertJsonPath('data.name', 'Supplier Export')
            ->assertJsonPath('data.layups.0.name', 'Layup A')
            ->assertJsonPath('data.layups.0.layers.0.layer_order', 1)
            ->assertJsonPath('data.layups.0.layers.0.thickness', 1.5);
    }
}
