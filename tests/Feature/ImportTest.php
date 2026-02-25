<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\CLT_Layup;
use App\Models\CLT_Layer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful import without conflicts.
     */
    public function test_import_without_conflicts()
    {
        // Create a supplier
        $supplier = Supplier::factory()->create();

        // Load sample JSON data
        $jsonContent = json_encode([
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                [
                    'name' => 'Test Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 10.0, 'width' => 100.0, 'angle' => 0.0],
                    ],
                ],
            ],
        ]);
        $jsonData = json_decode($jsonContent, true);

        // Create uploaded file
        $uploadedFile = UploadedFile::fake()->createWithContent('import.json', $jsonContent);

        // Upload the file
        $response = $this->actingAs($this->createUser())
            ->post(route('suppliers.import.upload', $supplier), [
                'json_file' => $uploadedFile,
            ]);

        $response->assertRedirect(route('suppliers.show', $supplier));

        // Verify data was imported
        $supplier->refresh();
        $this->assertCount(1, $supplier->layups);
        $layup = $supplier->layups->first();
        $this->assertEquals('Test Layup', $layup->name);
        $this->assertCount(1, $layup->layers);
    }

    /**
     * Test import with conflicts and resolution.
     */
    public function test_import_with_conflicts_and_resolution()
    {
        // Create a supplier with existing layup
        $supplier = Supplier::factory()->create();
        $existingLayup = CLT_Layup::factory()->create(['supplier_id' => $supplier->id]);
        CLT_Layer::factory()->create(['layup_id' => $existingLayup->id]);

        // Load sample JSON with conflicting layup name
        $jsonContent = json_encode([
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                [
                    'name' => $existingLayup->name, // Conflict
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 10.0, 'width' => 100.0, 'angle' => 0.0],
                    ],
                ],
            ],
        ]);

        // Upload the file
        $uploadedFile = UploadedFile::fake()->createWithContent('import.json', $jsonContent);

        $response = $this->actingAs($this->createUser())
            ->post(route('suppliers.import.upload', $supplier), [
                'json_file' => $uploadedFile,
            ]);

        $response->assertRedirect(route('suppliers.import.conflicts', $supplier));

        // Check conflicts page
        $response = $this->get(route('suppliers.import.conflicts', $supplier));
        $response->assertStatus(200);

        // Resolve conflicts (choose to replace)
        $response = $this->post(route('suppliers.import.resolve', $supplier), [
            'resolution' => [
                0 => 'overwrite', // Replace the conflicting layup
            ],
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));
        $response->assertSessionHas('success');

        // Verify the layup was replaced
        $supplier->refresh();
        $this->assertCount(1, $supplier->layups);
        $layup = $supplier->layups->first();
        $this->assertEquals($existingLayup->name, $layup->name);
        $this->assertCount(1, $layup->layers);
        $this->assertEquals(10.0, $layup->layers->first()->thickness);
    }

    /**
     * Test import form access.
     */
    public function test_import_form_access()
    {
        $supplier = $this->createSupplier();

        $response = $this->actingAs($this->createUser())
            ->get(route('suppliers.import.form', $supplier));

        $response->assertStatus(200);
        $response->assertViewIs('suppliers.import');
    }

    /**
     * Helper to create a user or supplier
     */
    private function createUser()
    {
        $user = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        return $user;
    }

    private function createSupplier()
    {
        return Supplier::with(['layups','layers'])->firstOrCreate([
            'name' => 'Test Supplier',
        ]);
    }
}
