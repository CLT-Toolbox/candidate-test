<?php

namespace Tests\Feature\Suppliers;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SupplierTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_export_includes_nested_layups_and_layers(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'Nordic CLT']);
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Panel Alpha']);
        Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 35,
            'width' => 120,
            'angle' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('suppliers.export', $supplier));

        $response->assertOk();

        $payload = json_decode($response->streamedContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('Nordic CLT', $payload['supplier']['name']);
        $this->assertSame('Panel Alpha', $payload['layups'][0]['name']);
        $this->assertSame(1, $payload['layups'][0]['layers'][0]['layer_order']);
    }

    public function test_import_with_overwrite_strategy_updates_conflicts_and_creates_missing_records(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Wall Panel']);
        $existingLayer = Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 40,
            'width' => 120,
            'angle' => 0,
        ]);

        $payload = [
            'supplier' => ['name' => 'Imported Supplier'],
            'layups' => [
                [
                    'name' => 'Wall Panel',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 55, 'width' => 150, 'angle' => 45],
                        ['layer_order' => 2, 'thickness' => 30, 'width' => 100, 'angle' => 0],
                    ],
                ],
                [
                    'name' => 'Roof Panel',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 25, 'width' => 90, 'angle' => -45],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('supplier.json', json_encode($payload, JSON_THROW_ON_ERROR));

        $this->actingAs($user)
            ->post(route('suppliers.import.preview', $supplier), [
                'import_file' => $file,
                'conflict_strategy' => 'overwrite',
            ])
            ->assertRedirect(route('suppliers.show', $supplier));

        $this->assertSame('55.00', $existingLayer->fresh()->thickness);
        $this->assertSame('150.00', $existingLayer->fresh()->width);
        $this->assertSame('45.00', $existingLayer->fresh()->angle);

        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 2,
        ]);

        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Roof Panel',
        ]);
    }

    public function test_manual_conflict_resolution_can_mix_keep_existing_and_accept_incoming(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Hybrid Panel']);

        $firstLayer = Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 110,
            'angle' => 0,
        ]);

        $secondLayer = Layer::factory()->for($layup)->create([
            'layer_order' => 2,
            'thickness' => 25,
            'width' => 115,
            'angle' => 45,
        ]);

        $payload = [
            'supplier' => ['name' => 'Manual Source'],
            'layups' => [
                [
                    'name' => 'Hybrid Panel',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 33, 'width' => 145, 'angle' => 90],
                        ['layer_order' => 2, 'thickness' => 27, 'width' => 118, 'angle' => -45],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('manual.json', json_encode($payload, JSON_THROW_ON_ERROR));

        $this->actingAs($user)
            ->post(route('suppliers.import.preview', $supplier), [
                'import_file' => $file,
                'conflict_strategy' => 'manual',
            ])
            ->assertRedirect(route('suppliers.import.conflicts', $supplier));

        $preview = session('supplier-import-preview.'.$supplier->id);

        $this->assertCount(2, $preview['conflicts']);

        $firstConflictKey = $preview['conflicts'][0]['key'];
        $secondConflictKey = $preview['conflicts'][1]['key'];

        $this->post(route('suppliers.import.resolve', $supplier), [
            'resolutions' => [
                $firstConflictKey => 'accept_incoming',
                $secondConflictKey => 'keep_existing',
            ],
        ])->assertRedirect(route('suppliers.show', $supplier));

        $this->assertSame('33.00', $firstLayer->fresh()->thickness);
        $this->assertSame('145.00', $firstLayer->fresh()->width);
        $this->assertSame('90.00', $firstLayer->fresh()->angle);

        $this->assertSame('25.00', $secondLayer->fresh()->thickness);
        $this->assertSame('115.00', $secondLayer->fresh()->width);
        $this->assertSame('45.00', $secondLayer->fresh()->angle);
    }
}
