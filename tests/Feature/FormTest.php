<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormTest extends TestCase
{
    /**
     * Test that the root url returns the form
     */
    public function test_the_application_root_form_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('main');
        $response->assertViewHas('groupTrees');
        $response->assertSeeHtml('<div id="app">');
    }
}
