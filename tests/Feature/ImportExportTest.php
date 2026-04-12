<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportExportTest extends TestCase
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

    public function test_can_export_supplier_data(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
        CltLayer::factory(3)->create(['layup_id' => $layup->id]);

        $response = $this->actingAs($this->user)->get(
            route('suppliers.export', $this->supplier)
        );

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertHeader('Content-Disposition');
    }

    public function test_can_detect_no_conflicts_for_new_import(): void
    {
        $importData = [
            'layups' => [
                [
                    'name' => 'New Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 40, 'width' => 1200, 'angle' => 0],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.detect-conflicts', $this->supplier),
            ['file' => $file]
        );

        $response->assertStatus(200);
        $this->assertFalse($response->json('has_conflicts'));
    }

    public function test_can_detect_layup_conflicts(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Existing Layup']);
        CltLayer::factory()->sequence(['layer_order' => 1, 'thickness' => 40])->create(['layup_id' => $layup->id]);

        $importData = [
            'layups' => [
                [
                    'name' => 'Existing Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 45, 'width' => 1200, 'angle' => 0],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.detect-conflicts', $this->supplier),
            ['file' => $file]
        );

        $response->assertStatus(200);
        $this->assertTrue($response->json('has_conflicts'));
        $this->assertCount(1, $response->json('conflicts'));
    }

    public function test_can_import_new_layups(): void
    {
        $importData = [
            'layups' => [
                [
                    'name' => 'Imported Layup',
                    'layup_code' => 'IMPORT-001',
                    'revision' => 'Rev 1',
                    'status' => 'active',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 40, 'width' => 1200, 'angle' => 0],
                        ['layer_order' => 2, 'thickness' => 35, 'width' => 1200, 'angle' => 90],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'skip', 'dry_run' => '0']
        );

        $this->assertDatabaseHas('clt_layups', ['name' => 'Imported Layup', 'supplier_id' => $this->supplier->id]);
        $this->assertDatabaseCount('clt_layers', 2);
    }

    public function test_skip_strategy_preserves_existing_data(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Existing']);
        $layer = CltLayer::factory()->sequence(['layer_order' => 1])->create(['layup_id' => $layup->id, 'thickness' => 40]);

        $importData = [
            'layups' => [
                [
                    'name' => 'Existing',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 50, 'width' => 1200, 'angle' => 0],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'skip', 'dry_run' => '0']
        );

        $layer->refresh();
        $this->assertEquals(40, $layer->thickness);
    }

    public function test_dry_run_does_not_save_data(): void
    {
        $initialLayupCount = CltLayup::count();

        $importData = [
            'layups' => [
                [
                    'name' => 'Test Layup',
                    'layup_code' => 'TEST-001',
                    'status' => 'active',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 40, 'width' => 1200, 'angle' => 0],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'skip', 'dry_run' => '1']
        );

        $this->assertEquals($initialLayupCount, CltLayup::count());
    }

    public function test_export_list_csv_format(): void
    {
        Supplier::factory()
            ->has(
                CltLayup::factory(2)->has(CltLayer::factory(2), 'layers'),
                'layups'
            )
            ->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.export-list-csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type');
    }

    public function test_export_list_json_format(): void
    {
        Supplier::factory(2)->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.export-list'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertHeader('Content-Disposition');
    }
}