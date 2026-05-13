<?php

namespace Tests\Unit;

use App\Exceptions\ImportConflictException;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Services\SupplierImportExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierImportExportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reject_strategy_throws_exception_when_conflict_exists(): void
    {
        $service = app(SupplierImportExportService::class);

        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'Conflict Layup']);
        Layer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 100,
            'angle' => 0,
        ]);

        $this->expectException(ImportConflictException::class);

        $service->importBySupplier($supplier, [
            'layups' => [
                [
                    'name' => 'Conflict Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 12,
                            'width' => 105,
                            'angle' => 10,
                        ],
                    ],
                ],
            ],
        ], SupplierImportExportService::STRATEGY_REJECT);
    }
}
