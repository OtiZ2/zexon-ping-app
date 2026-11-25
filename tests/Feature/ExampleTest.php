<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_api_returns_a_json_status(): void
    {
        $this->get('/api')->assertOk()->assertExactJson(['status' => 'ok']);
    }

    public function test_the_web_root_is_unavailable(): void
    {
        $this->get('/')->assertNotFound();
    }
}
