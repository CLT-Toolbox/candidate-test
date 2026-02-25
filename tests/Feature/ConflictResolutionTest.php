<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ConflictResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_conflict_strategy_overwrite(): void
    {
        $user = \App\Models\User::factory()->create();
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = $supplier->layups()->create(['name' => 'Layup 1']);
        $layup->layers()->create([
            'layer_order' => 1,
            'thickness' => 5.00,
            'width' => 100.00,
            'angle' => 45.00
        ]);
        
        $data = [
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 10, 'width' => 200, 'angle' => 90]
                    ]
                ]
            ]
        ];
        
        $file = UploadedFile::fake()->create('test.json', json_encode($data), 'application/json');
        
        $response = $this->actingAs($user)->post(route('dashboard.suppliers.import'), [
            'file' => $file,
            'conflict_strategy' => 'overwrite'
        ]);
        
        $response->assertRedirect();
        
        // Assert data was overwritten
        $this->assertDatabaseHas('clt_layers', [
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 200,
            'angle' => 90
        ]);
    }

    public function test_conflict_strategy_skip(): void
    {
        $user = \App\Models\User::factory()->create();
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = $supplier->layups()->create(['name' => 'Layup 1']);
        $layup->layers()->create([
            'layer_order' => 1,
            'thickness' => 5.00,
            'width' => 100.00,
            'angle' => 45.00
        ]);
        
        $data = [
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                [
                    'name' => 'Layup 1',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 10, 'width' => 200, 'angle' => 90]
                    ]
                ]
            ]
        ];
        
        $file = UploadedFile::fake()->create('test.json', json_encode($data), 'application/json');
        
        $response = $this->actingAs($user)->post(route('dashboard.suppliers.import'), [
            'file' => $file,
            'conflict_strategy' => 'skip'
        ]);
        
        $response->assertRedirect();
        
        // Assert data was NOT overwritten (kept original)
        $this->assertDatabaseHas('clt_layers', [
            'layer_order' => 1,
            'thickness' => 5,
            'width' => 100,
            'angle' => 45
        ]);
    }

    public function test_conflict_strategy_duplicate(): void
    {
        $user = \App\Models\User::factory()->create();
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $supplier->layups()->create(['name' => 'Layup 1']);
        
        $data = [
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                ['name' => 'Layup 1', 'layers' => []]
            ]
        ];
        
        $file = UploadedFile::fake()->create('test.json', json_encode($data), 'application/json');
        
        $response = $this->actingAs($user)->post(route('dashboard.suppliers.import'), [
            'file' => $file,
            'conflict_strategy' => 'duplicate'
        ]);
        
        $response->assertRedirect();
        
        // Assert duplicate was created with suffix
        $this->assertDatabaseHas('clt_layups', ['name' => 'Layup 1 (imported)']);
    }

    public function test_conflict_strategy_reject(): void
    {
        $user = \App\Models\User::factory()->create();
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $supplier->layups()->create(['name' => 'Layup 1']);
        
        $data = [
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                ['name' => 'Layup 1', 'layers' => []]
            ]
        ];
        
        $file = UploadedFile::fake()->create('test.json', json_encode($data), 'application/json');
        
        $response = $this->actingAs($user)->post(route('dashboard.suppliers.import'), [
            'file' => $file,
            'conflict_strategy' => 'reject'
        ]);
        
        $response->assertSessionHasErrors('file');
    }
}