<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\Layup;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LayupCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * Fitur: Create Layup
     * Skenario: User bisa membuat layup baru dengan nama yang valid
     */
    public function can_create_layup()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);

        $data = [
            'supplier_id' => $supplier->id,
            'name' => 'CLT Layup A',
        ];

        $response = $this->postJson('/api/layups', $data);

        $response->assertStatus(201);
        $response->assertJsonPath('name', 'CLT Layup A');

        $this->assertDatabaseHas('layups', [
            'name' => 'CLT Layup A',
            'supplier_id' => $supplier->id,
        ]);
    }

    /**
     * @test
     * Fitur: Read Layups (Index)
     * Skenario: User bisa melihat daftar semua layup
     */
    public function can_read_layups()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);

        Layup::create(['supplier_id' => $supplier->id, 'name' => 'Layup A']);
        Layup::create(['supplier_id' => $supplier->id, 'name' => 'Layup B']);
        Layup::create(['supplier_id' => $supplier->id, 'name' => 'Layup C']);

        $response = $this->getJson('/api/layups');

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJsonFragment(['name' => 'Layup A']);
        $response->assertJsonFragment(['name' => 'Layup B']);
        $response->assertJsonFragment(['name' => 'Layup C']);
    }

    /**
     * @test
     * Fitur: Read Single Layup (Show)
     * Skenario: User bisa melihat detail 1 layup berdasarkan ID
     */
    public function can_read_single_layup()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'Layup Tunggal']);

        $response = $this->getJson('/api/layups/' . $layup->id);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Layup Tunggal');
        $response->assertJsonPath('id', $layup->id);
    }

    /**
     * @test
     * Fitur: Update Layup
     * Skenario: User bisa mengubah nama layup yang sudah ada
     */
    public function can_update_layup()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'Nama Lama']);

        $data = ['name' => 'Nama Baru'];

        $response = $this->putJson('/api/layups/' . $layup->id, $data);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Nama Baru');

        $this->assertDatabaseHas('layups', [
            'id' => $layup->id,
            'name' => 'Nama Baru',
        ]);
    }

    /**
     * @test
     * Fitur: Delete Layup
     * Skenario: User bisa menghapus layup
     */
    public function can_delete_layup()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = Layup::create(['supplier_id' => $supplier->id, 'name' => 'Layup Hapus']);

        $response = $this->deleteJson('/api/layups/' . $layup->id);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Layup deleted successfully.');

        $this->assertDatabaseMissing('layups', [
            'id' => $layup->id,
        ]);
    }

    /**
     * @test
     * Fitur: Validation - Name is Required
     * Skenario: User tidak bisa create layup tanpa nama
     */
    public function layup_name_is_required()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);

        $data = [
            'supplier_id' => $supplier->id,
            'name' => '',
        ];

        $response = $this->postJson('/api/layups', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    /**
     * @test
     * Fitur: Validation - Supplier is Required
     * Skenario: User tidak bisa create layup tanpa supplier
     */
    public function layup_supplier_is_required()
    {
        $data = [
            'name' => 'Test Layup',
        ];

        $response = $this->postJson('/api/layups', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('supplier_id');
    }
}
