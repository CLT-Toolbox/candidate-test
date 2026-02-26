<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_export_supplier(): void {
        $user = \App\Models\User::factory()->create();
        $supplier = Supplier::create(['name' => 'Export Test']);
        $layup = $supplier->layups()->create(['name' => 'Layup 1']);
        $layup->layers()->create([
            'layer_order' => 1,
            'thickness' => 5.00,
            'width' => 100.00,
            'angle' => 45.00,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.suppliers.export', $supplier));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $response->assertJsonStructure([
            'supplier' => ['name'],
            'layups' => [['name', 'layers']],
        ]);
    }

    public function test_can_import_supplier(): void
    {
        $user = \App\Models\User::factory()->create();

        $data = [
            'supplier' => ['name' => 'Imported Supplier'],
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 5, 'width' => 100, 'angle' => 45],
                    ],
                ],
            ],
        ];

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.json', json_encode($data), 'application/json');

        $response = $this->actingAs($user)->post(route('dashboard.suppliers.import'), [
            'file' => $file,
            'conflict_strategy' => 'overwrite',
        ]);

        $response->assertRedirect(route('dashboard.suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Imported Supplier']);
    }

    public function test_import_validation_requires_file(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.suppliers.import'), [
            'conflict_strategy' => 'overwrite',
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_import_validation_requires_conflict_strategy(): void
    {
        $user = \App\Models\User::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.json', '{}', 'application/json');

        $response = $this->actingAs($user)->post(route('dashboard.suppliers.import'), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('conflict_strategy');
    }
}
