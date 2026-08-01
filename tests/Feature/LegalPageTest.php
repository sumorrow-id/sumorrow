<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_renders_in_english(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertOk();
        $response->assertSee('Privacy Policy');
        $response->assertSee('Information We Collect');

        // "On this page" nav links to every section anchor.
        $response->assertSee(__('api.on_this_page'));
        $response->assertSee('href="#section-1"', false);
        $response->assertSee('id="section-'.count(__('legal.privacy.sections')).'"', false);
    }

    public function test_terms_of_service_renders_in_english(): void
    {
        $response = $this->get('/terms-of-service');

        $response->assertOk();
        $response->assertSee('Terms of Service');
        $response->assertSee('Safety Disclaimer');
    }

    public function test_legal_pages_render_in_indonesian(): void
    {
        $this->get('/privacy-policy?lang=id')
            ->assertOk()
            ->assertSee('Kebijakan Privasi')
            ->assertSee('Data yang Kami Kumpulkan');

        $this->get('/terms-of-service?lang=id')
            ->assertOk()
            ->assertSee('Ketentuan Layanan')
            ->assertSee('Penafian Keselamatan');
    }

    /**
     * Both locales must define the same sections, or a page silently loses content.
     */
    public function test_both_locales_define_the_same_number_of_sections(): void
    {
        foreach (['privacy', 'terms'] as $page) {
            $this->assertCount(
                count(__("legal.$page.sections", locale: 'en')),
                __("legal.$page.sections", locale: 'id'),
                "Section count mismatch for '$page'"
            );
        }
    }

    public function test_footer_links_to_the_legal_pages(): void
    {
        $this->get('/home')
            ->assertSee(route('privacy'), false)
            ->assertSee(route('terms'), false);
    }
}
