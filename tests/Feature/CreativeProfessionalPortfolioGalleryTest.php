<?php

namespace Tests\Feature;

use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\ProfessionalCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreativeProfessionalPortfolioGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_creative_professionals_page_outputs_portfolio_images_for_all_categories(): void
    {
        // Create an active category (e.g. Makeup Artist)
        $category = ProfessionalCategory::firstOrCreate(
            ['slug' => 'makeup-artist'],
            [
                'name' => 'Makeup Artist',
                'description' => 'Editorial and bridal makeup artists',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        // Create a published creative professional with 2 portfolio images
        $pro = CreativeProfessional::create([
            'name' => 'Amina Makeup Studio',
            'category' => 'makeup-artist',
            'professional_title' => 'Lead Makeup Artist',
            'location' => 'Kochi, Kerala',
            'experience' => '5 years',
            'biography' => 'Experienced high-fashion and bridal makeup artist.',
            'services' => ['Bridal Makeup', 'Editorial Makeup', 'HD Airbrush'],
            'profile_photo' => 'uploads/professionals/test_pro.jpg',
            'portfolio_images' => [
                ['image' => 'uploads/professionals/test_port_1.jpg', 'caption' => 'Bridal Look 01'],
                ['image' => 'uploads/professionals/test_port_2.jpg', 'caption' => 'Editorial Look 02'],
            ],
            'status' => 'published',
            'display_order' => 1,
        ]);

        $response = $this->get(route('idfr.professionals'));
        $response->assertStatus(200);

        // Assert the creativeProsData template contains the professional
        $response->assertSee('Amina Makeup Studio');
        $response->assertSee('data-category="makeup-artist"', false);

        // Assert portfolio images are present inside the template
        $response->assertSee('test_port_1.jpg');
        $response->assertSee('test_port_2.jpg');
        $response->assertSee('Bridal Look 01');
        $response->assertSee('Editorial Look 02');
    }

    public function test_designer_portfolio_images_are_outputted_in_professionals_template(): void
    {
        // Create a published designer with 3 portfolio looks
        $designer = Designer::create([
            'name' => 'Vikram Singhania Couture',
            'brand_name' => 'Singhania Atelier',
            'location' => 'Mumbai, Maharashtra',
            'experience' => '8 years',
            'short_intro' => 'Heritage couture label.',
            'biography' => 'Handcrafted textiles and royal embroideries.',
            'services' => ['Bespoke Bridal', 'Couture Menswear', 'Runway Ensembles'],
            'profile_photo' => 'uploads/designers/vikram_profile.jpg',
            'portfolio_images' => [
                ['image' => 'uploads/designers/look_1.jpg', 'caption' => 'Velvet Sherwani'],
                ['image' => 'uploads/designers/look_2.jpg', 'caption' => 'Zardozi Lehenga'],
                ['image' => 'uploads/designers/look_3.jpg', 'caption' => 'Royal Heritage Dupatta'],
            ],
            'status' => 'published',
            'display_order' => 1,
        ]);

        $response = $this->get(route('idfr.professionals'));
        $response->assertStatus(200);

        $response->assertSee('Vikram Singhania Couture');
        $response->assertSee('Velvet Sherwani');
        $response->assertSee('Zardozi Lehenga');
        $response->assertSee('Royal Heritage Dupatta');
    }

    public function test_professional_with_only_profile_photo_gets_fallback_portfolio_look(): void
    {
        $pro = CreativeProfessional::create([
            'name' => 'Deepak Drone Lens',
            'category' => 'drone-pilot',
            'professional_title' => 'Commercial Drone Pilot',
            'location' => 'Calicut, Kerala',
            'profile_photo' => 'uploads/professionals/deepak_profile.jpg',
            'portfolio_images' => [],
            'status' => 'published',
            'display_order' => 1,
        ]);

        $portfolioItems = $pro->getPortfolioItems();
        $this->assertCount(1, $portfolioItems);
        $this->assertStringContainsString('deepak_profile.jpg', $portfolioItems[0]['url']);
        $this->assertEquals('Deepak Drone Lens', $portfolioItems[0]['caption']);

        $response = $this->get(route('idfr.professionals'));
        $response->assertStatus(200);
        $response->assertSee('Deepak Drone Lens');
        $response->assertSee('deepak_profile.jpg');
    }

    public function test_professional_with_no_images_or_photo_returns_empty_array_safely_and_homepage_loads(): void
    {
        $pro = CreativeProfessional::create([
            'name' => 'Blank Talent',
            'category' => 'hairstylist',
            'professional_title' => 'Hairstylist',
            'location' => 'Kochi, Kerala',
            'profile_photo' => null,
            'portfolio_images' => null,
            'status' => 'published',
            'display_order' => 99,
        ]);

        $portfolioItems = $pro->getPortfolioItems();
        $this->assertIsArray($portfolioItems);
        $this->assertEmpty($portfolioItems);

        // Verify that the homepage (idfr.home) renders successfully without any Undefined variable $items exception
        $response = $this->get(route('idfr.home'));
        $response->assertStatus(200);
        $response->assertSee('Blank Talent');
    }
}
