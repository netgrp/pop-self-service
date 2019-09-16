<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateResetRequestWorksTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_path_loads()
    {
        $response = $this->post('/resetPassword');

        // Assert we're getting a 302, since there are errors
        $response->assertStatus(302);
    }

    public function test_empty_respons_show_the_right_errors()
    {
        $response = $this->post('/resetPassword');

        // Assert errors since fields are empty

        // Email is required
        $response->assertSessionHasErrors('email');

        // Consent to save information about the request is required
        $response->assertSessionHasErrors('consent');

        // Recaptcha respons is required
        $response->assertSessionHasErrors('g-recaptcha-response');
    }
}
