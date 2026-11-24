<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public aspirations endpoint returns HTTP 200.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->getJson('/api/aspirations');

        $response->assertStatus(200);
    }
}
