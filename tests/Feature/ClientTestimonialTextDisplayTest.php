<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTestimonialTextDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    /**
     * Test admin can create a client with a very lengthy testimonial quote.
     */
    public function test_admin_can_create_client_with_lengthy_testimonial(): void
    {
        $longQuote = str_repeat('IDFR provided exceptional runway talent and unmatched professional coordination for our flagship season. ', 25);

        $response = $this->actingAs($this->adminUser, 'admin')->post(route('admin.clients.store'), [
            'name' => 'Haute Couture Studio',
            'industry' => 'Luxury Fashion',
            'testimonial_quote' => $longQuote,
            'author_name' => 'Priya Nair',
            'author_role' => 'Creative Director',
            'website_url' => 'https://hautecouture.example.com',
            'display_order' => 1,
            'is_active' => 1,
            'is_featured' => 1,
            'logo_style' => 'circle',
            'logo_mark' => 'H',
            'logo_word' => 'HAUTE',
            'logo_sub' => 'COUTURE',
        ]);

        $response->assertRedirect(route('admin.clients.index'));
        $this->assertDatabaseHas('clients', [
            'name' => 'Haute Couture Studio',
            'author_name' => 'Priya Nair',
            'is_featured' => 1,
        ]);

        $client = Client::where('name', 'Haute Couture Studio')->first();
        $this->assertNotNull($client);
        $this->assertSame(trim($longQuote), $client->testimonial_quote);
    }

    /**
     * Test admin can update an existing client with a lengthy testimonial quote.
     */
    public function test_admin_can_update_client_with_lengthy_testimonial(): void
    {
        $client = Client::create([
            'name' => 'Silk & Thread',
            'industry' => 'Ethnic Wear',
            'testimonial_quote' => 'Original short quote.',
            'author_name' => 'Aarav Shah',
            'author_role' => 'Founder',
            'display_order' => 2,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $updatedLongQuote = str_repeat('Our partnership with IDFR transformed how we execute fashion week campaigns across India. ', 20);

        $response = $this->actingAs($this->adminUser, 'admin')->put(route('admin.clients.update', $client), [
            'name' => 'Silk & Thread Atelier',
            'industry' => 'Ethnic Wear',
            'testimonial_quote' => $updatedLongQuote,
            'author_name' => 'Aarav Shah',
            'author_role' => 'Founder & CEO',
            'display_order' => 2,
            'is_active' => 1,
            'is_featured' => 0,
            'logo_style' => 'diamond',
            'logo_mark' => 'S',
            'logo_word' => 'SILK',
            'logo_sub' => 'ATELIER',
        ]);

        $response->assertRedirect(route('admin.clients.index'));
        $client->refresh();
        $this->assertSame('Silk & Thread Atelier', $client->name);
        $this->assertSame(trim($updatedLongQuote), $client->testimonial_quote);
    }

    /**
     * Test admin client forms include expanded textarea with overflow wrapping and styled preview quote.
     */
    public function test_admin_client_views_contain_responsive_wrapping_and_preview_styles(): void
    {
        $client = Client::create([
            'name' => 'Vogue Line',
            'industry' => 'Apparel',
            'testimonial_quote' => 'Test quote for preview testing.',
            'author_name' => 'Rohan Sen',
            'author_role' => 'Brand Manager',
            'display_order' => 1,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $createResponse = $this->actingAs($this->adminUser, 'admin')->get(route('admin.clients.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('rows="5"', false);
        $createResponse->assertSee('overflow-wrap: anywhere', false);
        $createResponse->assertSee('max-height: 280px', false);

        $editResponse = $this->actingAs($this->adminUser, 'admin')->get(route('admin.clients.edit', $client));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('rows="5"', false);
        $editResponse->assertSee('overflow-wrap: anywhere', false);
        $editResponse->assertSee('max-height: 280px', false);

        $indexResponse = $this->actingAs($this->adminUser, 'admin')->get(route('admin.clients.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('overflow-wrap: anywhere', false);
    }

    /**
     * Test client feature styles and JavaScript quote truncation and toggle logic.
     */
    public function test_client_feature_css_and_js_quote_expansion(): void
    {
        $publicCss = file_get_contents(public_path('css/style.css'));
        $this->assertStringContainsString('.client-feature__quote', $publicCss);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $publicCss);
        $this->assertStringContainsString('.client-feature__toggle', $publicCss);

        $publicJs = file_get_contents(public_path('js/script.js'));
        $this->assertStringContainsString('renderFeaturedQuote', $publicJs);
        $this->assertStringContainsString('client-feature__toggle', $publicJs);
        $this->assertStringContainsString('Read More', $publicJs);
        $this->assertStringContainsString('Show Less', $publicJs);

        $staticJs = file_get_contents(base_path('IDFR_LATEST/js/script.js'));
        $this->assertStringContainsString('renderFeaturedQuote', $staticJs);
        $this->assertStringContainsString('client-feature__toggle', $staticJs);
    }
}
