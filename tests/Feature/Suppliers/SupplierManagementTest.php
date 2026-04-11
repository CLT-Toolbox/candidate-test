<?php

namespace Tests\Feature\Suppliers;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_supplier_layup_and_layer_hierarchy(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('suppliers.store'), [
                'name' => 'Atlas Timber',
            ])
            ->assertRedirect();

        $supplier = Supplier::query()->firstOrFail();

        $this->post(route('suppliers.layups.store', $supplier), [
            'name' => 'Wall Panel A',
        ])->assertRedirect(route('suppliers.show', $supplier));

        $layup = Layup::query()->firstOrFail();

        $this->post(route('suppliers.layups.layers.store', [$supplier, $layup]), [
            'layer_order' => 1,
            'thickness' => 42.5,
            'width' => 120,
            'angle' => 0,
        ])->assertRedirect(route('suppliers.show', $supplier));

        $layer = Layer::query()->firstOrFail();

        $this->patch(route('suppliers.update', $supplier), [
            'name' => 'Atlas Timber Updated',
        ])->assertRedirect(route('suppliers.show', $supplier));

        $this->patch(route('suppliers.layups.update', [$supplier, $layup]), [
            'name' => 'Roof Panel B',
        ])->assertRedirect(route('suppliers.show', $supplier));

        $this->patch(route('suppliers.layups.layers.update', [$supplier, $layup, $layer]), [
            'layer_order' => 2,
            'thickness' => 55.75,
            'width' => 140,
            'angle' => 45,
        ])->assertRedirect(route('suppliers.show', $supplier));

        $this->assertSame('Atlas Timber Updated', $supplier->fresh()->name);
        $this->assertSame('Roof Panel B', $layup->fresh()->name);

        $layer = $layer->fresh();

        $this->assertSame(2, $layer->layer_order);
        $this->assertSame('55.75', $layer->thickness);
        $this->assertSame('140.00', $layer->width);
        $this->assertSame('45.00', $layer->angle);

        $this->delete(route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]))
            ->assertRedirect(route('suppliers.show', $supplier));

        $this->delete(route('suppliers.layups.destroy', [$supplier, $layup]))
            ->assertRedirect(route('suppliers.show', $supplier));

        $this->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect(route('suppliers.index'));

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
