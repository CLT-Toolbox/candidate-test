<?php

namespace Tests\Feature\Api;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_succeeds_with_overwrite_strategy(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Supplier Import']);
        $existingLayup = Layup::factory()->for($supplier)->create(['name' => 'Layup A']);
        Layer::factory()->for($existingLayup)->create([
            'layer_order' => 1,
            'thickness' => 1.1,
            'width' => 9,
            'angle' => 10,
        ]);

        $response = $this->postJson("/api/suppliers/{$supplier->id}/import", [
            'strategy' => 'overwrite',
            'layups' => [
                [
                    'name' => 'Layup A',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 1.2,
                            'width' => 10,
                            'angle' => 45,
                        ],
                        [
                            'layer_order' => 2,
                            'thickness' => 0.8,
                            'width' => 8,
                            'angle' => -45,
                        ],
                    ],
                ],
                [
                    'name' => 'Layup B',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 2.2,
                            'width' => 20,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.resolution_applied', 'overwrite')
            ->assertJsonPath('data.conflicts_found', 1)
            ->assertJsonPath('data.affected_records.layups_created', 1)
            ->assertJsonPath('data.affected_records.layups_reused', 1)
            ->assertJsonPath('data.affected_records.layers_created', 2)
            ->assertJsonPath('data.affected_records.layers_updated', 1);

        $this->assertDatabaseHas('layers', [
            'layup_id' => $existingLayup->id,
            'layer_order' => 1,
            'thickness' => 1.2,
            'width' => 10,
            'angle' => 45,
        ]);

        $this->assertDatabaseHas('layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Layup B',
        ]);
    }

    public function test_import_rejects_and_returns_conflict_report(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Layup A']);
        Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 1.1,
            'width' => 9,
            'angle' => 10,
        ]);

        $response = $this->postJson("/api/suppliers/{$supplier->id}/import", [
            'strategy' => 'reject',
            'layups' => [
                [
                    'name' => 'Layup A',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 4.4,
                            'width' => 22,
                            'angle' => 15,
                        ],
                    ],
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data.strategy', 'reject')
            ->assertJsonPath('data.conflicts_found', 1)
            ->assertJsonPath('data.conflicts.0.layer_order', 1);

        $this->assertDatabaseHas('layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 1.1,
            'width' => 9,
            'angle' => 10,
        ]);
    }
}
