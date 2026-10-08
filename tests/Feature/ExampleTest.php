<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_root_redirects_to_the_inventory_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('filament.wizy.auth.login'));
    }
}
