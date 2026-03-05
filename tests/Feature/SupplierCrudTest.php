<?php

namespace Tests\Feature;

// =========================================================================
// IMPORT DEPENDENCIES
// =========================================================================
// Tests\TestCase = Base class untuk semua test (ada setup database, dll)
// App\Models\Supplier = Model Supplier yang akan kita test
// Illuminate\Foundation\Testing\RefreshDatabase = Trait untuk reset database setiap test
use Tests\TestCase;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

// =========================================================================
// CLASS TEST
// =========================================================================
// extends TestCase = Class ini "inherit" dari TestCase (dapat fitur testing Laravel)
// RefreshDatabase = Trait yang otomatis reset database sebelum setiap test
// Ini memastikan setiap test mulai dari kondisi bersih (tidak ada data sisa)
class SupplierCrudTest extends TestCase
{
    // =========================================================================
    // TRAIT: RefreshDatabase
    // =========================================================================
    // use = Memakai trait (seperti "plugin" untuk class)
    // RefreshDatabase = Setiap test akan:
    //   1. Rollback semua perubahan database setelah test selesai
    //   2. Test berjalan di transaction (tidak benar-benar save ke database)
    //   3. Setiap test independen, tidak terpengaruh test lain
    use RefreshDatabase;

    // =========================================================================
    // TEST 1: Can Create Supplier
    // =========================================================================
    /**
     * @test
     * Fitur: Create Supplier
     * Skenario: User bisa membuat supplier baru dengan nama yang valid
     */
    public function can_create_supplier()
    {
        // =======================================================================
        // ARRANGE: Persiapkan data yang dibutuhkan
        // =======================================================================
        // Data yang akan kita kirim ke API
        $data = [
            'name' => 'PT. Kayu Lapis Indonesia'
        ];

        // =======================================================================
        // ACT: Jalankan aksi yang mau di-test
        // =======================================================================
        // postJson() = Kirim request POST dengan format JSON ke endpoint
        // '/api/suppliers' = URL endpoint (route apiResource)
        // $data = Data yang dikirim dalam body request
        $response = $this->postJson('/api/suppliers', $data);

        // =======================================================================
        // ASSERT: Cek apakah hasilnya sesuai ekspektasi
        // =======================================================================
        // assertStatus(201) = Cek HTTP status code harus 201 (Created)
        // 201 = Resource berhasil dibuat
        $response->assertStatus(201);

        // assertJson() = Cek response mengandung JSON seperti yang diharapkan
        $response->assertJson([
            'name' => 'PT. Kayu Lapis Indonesia'
        ]);

        // assertDatabaseHas() = Cek database benar-benar punya data ini
        // 'suppliers' = Nama tabel
        // ['name' => ...] = Kondisi WHERE (cek ada record dengan nama ini)
        $this->assertDatabaseHas('suppliers', [
            'name' => 'PT. Kayu Lapis Indonesia'
        ]);
    }

    // =========================================================================
    // TEST 2: Can Read/List Suppliers
    // =========================================================================
    /**
     * @test
     * Fitur: Read Suppliers (Index)
     * Skenario: User bisa melihat daftar semua supplier
     */
    public function can_read_suppliers()
    {
        // =======================================================================
        // ARRANGE: Buat beberapa supplier dummy untuk di-test
        // =======================================================================
        // create() = Method Eloquent untuk insert 1 record ke database
        // Factory tidak perlu karena model sederhana, bisa langsung create()
        Supplier::create(['name' => 'Supplier A']);
        Supplier::create(['name' => 'Supplier B']);
        Supplier::create(['name' => 'Supplier C']);

        // =======================================================================
        // ACT: Request ke endpoint index
        // =======================================================================
        // getJson() = Kirim request GET dengan format JSON
        // '/api/suppliers' = Endpoint untuk list semua supplier
        $response = $this->getJson('/api/suppliers');

        // =======================================================================
        // ASSERT: Cek hasilnya
        // =======================================================================
        // assertStatus(200) = Cek HTTP status harus 200 (OK)
        $response->assertStatus(200);

        // assertJsonCount() = Cek jumlah array dalam response
        // 3 = Harus ada 3 supplier (karena kita create 3 di atas)
        // 'suppliers' tidak perlu karena response langsung array
        $response->assertJsonCount(3);

        // assertJsonFragment() = Cek response mengandung data ini (partial match)
        $response->assertJsonFragment(['name' => 'Supplier A']);
        $response->assertJsonFragment(['name' => 'Supplier B']);
        $response->assertJsonFragment(['name' => 'Supplier C']);
    }

    // =========================================================================
    // TEST 3: Can Read Single Supplier
    // =========================================================================
    /**
     * @test
     * Fitur: Read Single Supplier (Show)
     * Skenario: User bisa melihat detail 1 supplier berdasarkan ID
     */
    public function can_read_single_supplier()
    {
        // =======================================================================
        // ARRANGE: Buat 1 supplier untuk di-test
        // =======================================================================
        $supplier = Supplier::create(['name' => 'Supplier Tunggal']);

        // =======================================================================
        // ACT: Request detail supplier
        // =======================================================================
        // '/api/suppliers/' . $supplier->id = URL seperti /api/suppliers/1
        // Route Model Binding akan otomatis resolve ID ke model
        $response = $this->getJson('/api/suppliers/' . $supplier->id);

        // =======================================================================
        // ASSERT: Cek hasilnya
        // =======================================================================
        $response->assertStatus(200);

        // assertJsonPath() = Cek value di path tertentu dalam JSON
        // 'name' = Path ke field 'name'
        // 'Supplier Tunggal' = Value yang diharapkan
        $response->assertJsonPath('name', 'Supplier Tunggal');

        // assertJsonPath() lagi untuk cek ID
        $response->assertJsonPath('id', $supplier->id);
    }

    // =========================================================================
    // TEST 4: Can Update Supplier
    // =========================================================================
    /**
     * @test
     * Fitur: Update Supplier
     * Skenario: User bisa mengubah nama supplier yang sudah ada
     */
    public function can_update_supplier()
    {
        // =======================================================================
        // ARRANGE: Buat supplier dulu (yang mau di-update)
        // =======================================================================
        $supplier = Supplier::create(['name' => 'Nama Lama']);

        // Data baru untuk update
        $data = [
            'name' => 'Nama Baru'
        ];

        // =======================================================================
        // ACT: Kirim request PUT untuk update
        // =======================================================================
        // putJson() = Kirim request PUT dengan format JSON
        // PUT = HTTP method untuk update (selain POST untuk create)
        $response = $this->putJson('/api/suppliers/' . $supplier->id, $data);

        // =======================================================================
        // ASSERT: Cek hasilnya
        // =======================================================================
        $response->assertStatus(200);

        // Cek response mengandung nama baru
        $response->assertJsonPath('name', 'Nama Baru');

        // Cek database benar-benar ter-update
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Nama Baru'
        ]);

        // Pastikan nama lama sudah tidak ada
        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
            'name' => 'Nama Lama'
        ]);
    }

    // =========================================================================
    // TEST 5: Can Delete Supplier
    // =========================================================================
    /**
     * @test
     * Fitur: Delete Supplier
     * Skenario: User bisa menghapus supplier
     */
    public function can_delete_supplier()
    {
        // =======================================================================
        // ARRANGE: Buat supplier yang akan dihapus
        // =======================================================================
        $supplier = Supplier::create(['name' => 'Supplier Hapus']);

        // =======================================================================
        // ACT: Kirim request DELETE
        // =======================================================================
        // deleteJson() = Kirim request DELETE dengan format JSON
        // DELETE = HTTP method untuk menghapus resource
        $response = $this->deleteJson('/api/suppliers/' . $supplier->id);

        // =======================================================================
        // ASSERT: Cek hasilnya
        // =======================================================================
        $response->assertStatus(200);

        // Cek response ada pesan sukses
        $response->assertJsonPath('message', 'Supplier berhasil dihapus');

        // Cek database - supplier harus sudah tidak ada
        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id
        ]);
    }

    // =========================================================================
    // TEST 6: Validation - Name is Required
    // =========================================================================
    /**
     * @test
     * Fitur: Validation
     * Skenario: User tidak bisa create supplier tanpa nama
     */
    public function supplier_name_is_required()
    {
        // =======================================================================
        // ARRANGE: Data tidak valid (nama kosong)
        // =======================================================================
        $data = [
            'name' => ''  // Nama kosong - tidak valid!
        ];

        // =======================================================================
        // ACT: Coba create supplier
        // =======================================================================
        $response = $this->postJson('/api/suppliers', $data);

        // =======================================================================
        // ASSERT: Harus ditolak dengan error 422
        // =======================================================================
        // 422 = Unprocessable Entity - Request valid tapi data tidak valid
        $response->assertStatus(422);

        // assertJsonValidationErrors() = Cek ada error validasi untuk field 'name'
        $response->assertJsonValidationErrors('name');
    }

    // =========================================================================
    // TEST 7: Validation - Name Max Length
    // =========================================================================
    /**
     * @test
     * Fitur: Validation
     * Skenario: User tidak bisa create supplier dengan nama > 255 karakter
     */
    public function supplier_name_max_255_characters()
    {
        // =======================================================================
        // ARRANGE: Nama terlalu panjang (256 karakter)
        // =======================================================================
        // str_repeat() = Mengulang string sebanyak N kali
        // str_pad() = Padding string sampai panjang tertentu
        $data = [
            'name' => str_repeat('a', 256)  // 256 karakter - terlalu panjang!
        ];

        // =======================================================================
        // ACT: Coba create supplier
        // =======================================================================
        $response = $this->postJson('/api/suppliers', $data);

        // =======================================================================
        // ASSERT: Harus ditolak
        // =======================================================================
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}
