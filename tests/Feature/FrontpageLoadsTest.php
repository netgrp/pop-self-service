<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontpageLoadsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFrontpageLoads()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testFrontpageContainsForm()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertSee('reset-request sitekey=');
    }
}
