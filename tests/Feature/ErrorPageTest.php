<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    public function test_404_renders_custom_error_page(): void
    {
        $response = $this->get('/definitely-not-a-real-page');

        $response->assertNotFound();
        $response->assertSee('Page not found');
        $response->assertSee('Back to Home');
    }

    public function test_413_renders_upload_too_large_page(): void
    {
        Route::middleware('web')->get('/_test-413', fn () => abort(413));

        $response = $this->get('/_test-413');

        $response->assertStatus(413);
        $response->assertSee('Upload too large');
    }

    public function test_unmapped_status_falls_back_to_default_message(): void
    {
        Route::middleware('web')->get('/_test-400', fn () => abort(400));

        $response = $this->get('/_test-400');

        $response->assertStatus(400);
        $response->assertSee('Something went wrong');
    }

    public function test_api_404_still_returns_json_envelope(): void
    {
        $this->getJson('/api/v1/definitely-not-a-real-endpoint')
            ->assertNotFound()
            ->assertJson(['status' => 404]);
    }
}
