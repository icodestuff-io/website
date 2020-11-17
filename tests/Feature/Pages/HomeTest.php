<?php

namespace Tests\Feature\Pages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_i_can_see_icodestuff_on_the_home_page()
    {
        $response = $this->get('/');

        $response->assertSee('Icodestuff');
        $response->assertStatus(200);
    }
}
