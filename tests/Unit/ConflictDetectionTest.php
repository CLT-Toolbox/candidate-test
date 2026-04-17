<?php

namespace Tests\Unit;

use App\Services\ImportService;
use App\Services\ConflictDetectionService;
use Tests\TestCase;

class ConflictDetectionTest extends TestCase
{
    protected ConflictDetectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConflictDetectionService();
    }

    public function test_detect_no_conflicts_when_incoming_matches_existing()
    {
        $incomingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 2.5,
                    'width' => 100,
                    'angle' => 45,
                ],
            ],
        ];

        $existingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 2.5,
                    'width' => 100,
                    'angle' => 45,
                ],
            ],
        ];

        $conflicts = $this->service->detectConflicts($incomingData, $existingData);

        $this->assertEmpty($conflicts);
    }

    public function test_detect_conflict_when_layer_thickness_differs()
    {
        $incomingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 3.0,
                    'width' => 100,
                    'angle' => 45,
                ],
            ],
        ];

        $existingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 2.5,
                    'width' => 100,
                    'angle' => 45,
                ],
            ],
        ];

        $conflicts = $this->service->detectConflicts($incomingData, $existingData);

        $this->assertNotEmpty($conflicts);
        $this->assertEquals(ConflictDetectionService::CONFLICT_TYPE_LAYER, $conflicts[0]['type']);
        $this->assertEquals(1, $conflicts[0]['layer_order']);
    }

    public function test_detect_conflict_when_layer_width_differs()
    {
        $incomingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 2.5,
                    'width' => 150,
                    'angle' => 45,
                ],
            ],
        ];

        $existingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 2.5,
                    'width' => 100,
                    'angle' => 45,
                ],
            ],
        ];

        $conflicts = $this->service->detectConflicts($incomingData, $existingData);

        $this->assertNotEmpty($conflicts);
    }

    public function test_detect_conflict_when_layer_angle_differs()
    {
        $incomingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 2.5,
                    'width' => 100,
                    'angle' => 90,
                ],
            ],
        ];

        $existingData = [
            'name' => 'Layup A',
            'layers' => [
                [
                    'layer_order' => 1,
                    'thickness' => 2.5,
                    'width' => 100,
                    'angle' => 45,
                ],
            ],
        ];

        $conflicts = $this->service->detectConflicts($incomingData, $existingData);

        $this->assertNotEmpty($conflicts);
    }
}
