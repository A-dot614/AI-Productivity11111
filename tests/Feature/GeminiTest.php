<?php

namespace Tests\Feature;

use Tests\TestCase;

class GeminiTest extends TestCase
{
    public function test_get_gemini_generate_redirects_to_the_form(): void
    {
        $response = $this->get('/gemini/generate');

        $response->assertRedirect('/gemini');
    }
}
