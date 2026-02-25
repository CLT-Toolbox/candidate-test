<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_supplier_structure(): void
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = $supplier->layups()->create(['name' => 'Layup 1']);
        $layup->layers()->create([
            'layer_order' => 1,
            'thickness' => 5.00,
            'width' => 100.00,
            'angle' => 45.00
        ]);
        
        $service = new ExportService();
        $result = $service->exportSupplier($supplier);
        
        $this->assertArrayHasKey('supplier', $result);
        $this->assertArrayHasKey('layups', $result);
        $this->assertEquals('Test Supplier', $result['supplier']['name']);
        $this->assertCount(1, $result['layups']);
    }
}