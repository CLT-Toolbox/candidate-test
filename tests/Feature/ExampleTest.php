<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_returns_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200); // Welcome page exists now
    }
}