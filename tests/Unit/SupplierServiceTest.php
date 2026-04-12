<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SupplierService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SupplierService::class);
    }

    public function test_can_create_supplier(): void
    {
        $data = [
            'name' => 'Test Supplier',
            'primary_contact' => 'test@example.com',
            'location' => 'Toronto',
        ];

        $supplier = $this->service->create($data);

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertEquals('Test Supplier', $supplier->name);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $data = ['name' => 'Updated Name'];
        $this->service->update($supplier, $data);

        $supplier->refresh();
        $this->assertEquals('Updated Name', $supplier->name);
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        $id = $supplier->id;

        $this->service->delete($supplier);

        $this->assertDatabaseMissing('suppliers', ['id' => $id]);
    }

    public function test_can_paginate_suppliers(): void
    {
        Supplier::factory(25)->create();

        $paginated = $this->service->paginate(10);

        $this->assertEquals(10, $paginated->count());
    }
}