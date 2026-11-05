<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactResponsiveLayoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test contact page renders successfully with both sections.
     */
    public function test_contact_page_renders_with_enquiry_and_contact_info_sections(): void
    {
        $response = $this->get(route('idfr.contact'));
        $response->assertStatus(200);

        // Sections exist in the layout container
        $response->assertSee('asd-contact__layout', false);
        $response->assertSee('asd-contact__info', false);
        $response->assertSee('asd-contact__form', false);
        $response->assertSee('Contact <em>Information</em>', false);
        $response->assertSee('Send an <em>Enquiry</em>', false);
    }

    /**
     * Test asdreams contact page renders successfully with both sections.
     */
    public function test_asdreams_contact_page_renders_with_enquiry_and_contact_info_sections(): void
    {
        $response = $this->get(route('asd.contact'));
        $response->assertStatus(200);

        $response->assertSee('asd-contact__layout', false);
        $response->assertSee('asd-contact__info', false);
        $response->assertSee('asd-contact__form', false);
        $response->assertSee('Contact <em>Information</em>', false);
        $response->assertSee('Send an <em>Enquiry</em>', false);
    }

    /**
     * Test CSS files preserve desktop 2-column layout and implement mobile order: Send an Enquiry first, Contact Information second.
     */
    public function test_css_files_have_desktop_and_mobile_responsive_order_rules(): void
    {
        $cssPaths = [
            public_path('css/style.css'),
            base_path('IDFR_LATEST/css/style.css'),
        ];

        foreach ($cssPaths as $path) {
            $this->assertFileExists($path);
            $css = file_get_contents($path);

            // Desktop layout: 2-column side-by-side
            $this->assertMatchesRegularExpression(
                '/\.asd-contact__layout\s*\{[^}]*grid-template-columns:\s*minmax\(0,\s*0\.92fr\)\s*minmax\(0,\s*1\.08fr\)/',
                $css,
                "Desktop layout definition missing in {$path}"
            );

            // Responsive Mobile View: Send an Enquiry (order: 1) first, Contact Information (order: 2) second
            $this->assertMatchesRegularExpression(
                '/\.asd-contact__form\s*\{[^}]*order:\s*1;/',
                $css,
                "Mobile order 1 for asd-contact__form missing in {$path}"
            );

            $this->assertMatchesRegularExpression(
                '/\.asd-contact__info\s*\{[^}]*order:\s*2;/',
                $css,
                "Mobile order 2 for asd-contact__info missing in {$path}"
            );

            $this->assertStringContainsString(
                '.asd-contact__layout {',
                $css
            );
            $this->assertStringContainsString(
                'order: 1;',
                $css
            );
            $this->assertStringContainsString(
                'order: 2;',
                $css
            );
        }
    }
}
