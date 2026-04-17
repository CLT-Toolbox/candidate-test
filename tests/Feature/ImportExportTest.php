<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Layup;
use App\Models\Layer;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_export_supplier_data()
    {
        $supplier = Supplier::factory()->create(['name' => 'Test Supplier']);
        $layup = Layup::factory()->for($supplier)->create(['name' => 'Test Layup']);
        Layer::factory()->for($layup)->create([
            'layer_order' => 1,
            'thickness' => 2.5,
            'width' => 100,
            'angle' => 45,
        ]);

        $response = $this->actingAs($this->user)->get(route('suppliers.export', $supplier));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals('Test Supplier', $data['name']);
        $this->assertCount(1, $data['layups']);
        $this->assertEquals('Test Layup', $data['layups'][0]['name']);
        $this->assertCount(1, $data['layups'][0]['layers']);
    }

    public function test_export_service_includes_all_relations()
    {
        $supplier = Supplier::factory()->create();
        $layup1 = Layup::factory()->for($supplier)->create();
        $layup2 = Layup::factory()->for($supplier)->create();

        Layer::factory()->for($layup1)->count(3)->create();
        Layer::factory()->for($layup2)->count(2)->create();

        $service = new ExportService();
        $data = $service->export($supplier);

        $this->assertCount(2, $data['layups']);
        $this->assertCount(3, $data['layups'][0]['layers']);
        $this->assertCount(2, $data['layups'][1]['layers']);
    }

    public function test_user_can_import_supplier_data()
    {
        $importData = [
            'name' => 'Imported Supplier',
            'layups' => [
                [
                    'name' => 'Imported Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 2.5,
                            'width' => 100,
                            'angle' => 45,
                        ],
                    ],
                ],
            ],
        ];

        $file = $this->createTempJsonFile($importData);

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import'),
            ['file' => $file]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'Imported Supplier']);
        $this->assertDatabaseHas('clt_layups', ['name' => 'Imported Layup']);
    }

    private function createTempJsonFile(array $data)
    {
        $jsonContent = json_encode($data);
        
        // Create a fake file with JSON content
        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            $jsonContent,
            'application/json'
        );
        
        return $file;
    }
}
