<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_has_many_layups(): void
    {
        $supplier = Supplier::factory()->create();
        $layups = CltLayup::factory(3)->create(['supplier_id' => $supplier->id]);

        $this->assertCount(3, $supplier->layups);
        $this->assertTrue($supplier->layups->contains($layups->first()));
    }

    public function test_layup_belongs_to_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);

        $this->assertTrue($layup->supplier->is($supplier));
    }

    public function test_layup_has_many_layers(): void
    {
        $layup = CltLayup::factory()->create();
        $layers = [];
        for ($i = 1; $i <= 5; $i++) {
            $layers[] = CltLayer::factory()->create([
                'layup_id' => $layup->id,
                'layer_order' => $i,
            ]);
        }

        $this->assertCount(5, $layup->layers);
    }

    public function test_layer_belongs_to_layup(): void
    {
        $layup = CltLayup::factory()->create();
        $layer = CltLayer::factory()->sequence(['layer_order' => 1])->create(['layup_id' => $layup->id]);

        $this->assertTrue($layer->layup->is($layup));
    }

    public function test_layup_get_total_thickness(): void
    {
        $layup = CltLayup::factory()->create();
        CltLayer::factory()->sequence(['layer_order' => 1, 'thickness' => 40])->create(['layup_id' => $layup->id]);
        CltLayer::factory()->sequence(['layer_order' => 2, 'thickness' => 35])->create(['layup_id' => $layup->id]);

        $this->assertEquals(75, $layup->getTotalThicknessAttribute());
    }

    public function test_layup_get_ply_count(): void
    {
        $layup = CltLayup::factory()->create();
        for ($i = 1; $i <= 5; $i++) {
            CltLayer::factory()->create([
                'layup_id' => $layup->id,
                'layer_order' => $i,
            ]);
        }

        $this->assertEquals(5, $layup->getPlyCountAttribute());
    }

    public function test_supplier_has_fillable_fields(): void
    {
        $supplier = Supplier::factory()->make([
            'name' => 'Test',
            'primary_contact' => 'test@example.com',
            'location' => 'Toronto',
            'material_certifications' => 'SPF',
        ]);

        $this->assertEquals('Test', $supplier->name);
        $this->assertEquals('test@example.com', $supplier->primary_contact);
    }
}