<?php

use App\Models\CategoryFormField;
use App\Models\ProfessionalCategory;
use App\Models\ProfessionalFormField;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('professional_form_fields') || ! Schema::hasTable('professional_categories') || ! Schema::hasTable('category_form_fields')) {
            return;
        }

        $specialisationsData = [
            'makeup-artist' => [
                'field_key' => 'service_makeup_artist',
                'label' => 'Makeup Styles & Specialisations',
                'options' => [
                    'bridal-makeup' => 'Bridal makeup',
                    'engagement-makeup' => 'Engagement makeup',
                    'fashion-show-makeup' => 'Fashion-show makeup',
                    'editorial-makeup' => 'Editorial makeup',
                    'party-makeup' => 'Party makeup',
                    'hd-airbrush-makeup' => 'HD / airbrush makeup',
                    'natural-no-makeup-looks' => 'Natural & no-makeup looks',
                    'pre-wedding-shoot-makeup' => 'Pre-wedding shoot makeup',
                ],
            ],
            'hairstylist' => [
                'field_key' => 'service_hairstylist',
                'label' => 'Hairstyling Specialisations',
                'options' => [
                    'bridal-hairstyling' => 'Bridal hairstyling',
                    'editorial-hairstyles' => 'Editorial hairstyles',
                    'fashion-show-styling' => 'Fashion-show styling',
                    'hair-updos' => 'Hair updos',
                    'event-hairstyling' => 'Event hairstyling',
                    'colour-highlights' => 'Colour & highlights',
                    'hair-extensions' => 'Hair extensions',
                    'quick-change-backstage-looks' => 'Quick-change backstage looks',
                ],
            ],
            'photographer' => [
                'field_key' => 'service_photographer',
                'label' => 'Photography Specialisations',
                'options' => [
                    'wedding-photography' => 'Wedding photography',
                    'destination-shoots' => 'Destination shoots',
                    'pre-wedding-shoots' => 'Pre-wedding shoots',
                    'portrait-sessions' => 'Portrait sessions',
                    'fashion-editorial' => 'Fashion & editorial',
                    'model-portfolios' => 'Model portfolios',
                    'event-photography' => 'Event photography',
                    'album-design' => 'Album design',
                ],
            ],
            'videographer' => [
                'field_key' => 'service_videographer',
                'label' => 'Videography Specialisations',
                'options' => [
                    'cinematic-wedding-films' => 'Cinematic wedding films',
                    'fashion-films' => 'Fashion films',
                    'highlight-reels' => 'Highlight reels',
                    'colour-grading' => 'Colour grading',
                    'drone-videography' => 'Drone videography',
                    'brand-promotional-videos' => 'Brand & promotional videos',
                    'live-event-filming' => 'Live event filming',
                    'multi-camera-production' => 'Multi-camera production',
                ],
            ],
            'fashion-designer' => [
                'field_key' => 'service_fashion_designer',
                'label' => 'Design Focus & Specialisations',
                'options' => [
                    'bridal-and-occasion-wear' => 'Bridal and occasion wear',
                    'custom-couture' => 'Custom couture',
                    'handloom-collections' => 'Handloom collections',
                    'styling-consultations' => 'Styling consultations',
                    'runway-collections' => 'Runway collections',
                    'ready-to-wear' => 'Ready-to-wear',
                    'ethnic-fusion-wear' => 'Ethnic & fusion wear',
                    'lookbook-styling' => 'Lookbook styling',
                ],
            ],
            'drone-pilot' => [
                'field_key' => 'service_drone_pilot',
                'label' => 'Drone Operation Specialisations',
                'options' => [
                    'aerial-wedding-films' => 'Aerial wedding films',
                    'destination-drone-coverage' => 'Destination drone coverage',
                    'real-estate-flyovers' => 'Real-estate flyovers',
                    'fpv-accent-shots' => 'FPV accent shots',
                    'dgca-compliant-operations' => 'DGCA-compliant operations',
                    'aerial-photography' => 'Aerial photography',
                    'event-overhead-coverage' => 'Event overhead coverage',
                    'mapping-site-surveys' => 'Mapping & site surveys',
                ],
            ],
            'photo-video-editor' => [
                'field_key' => 'service_photo_video_editor',
                'label' => 'Editing Specialisations',
                'options' => [
                    'wedding-film-editing' => 'Wedding film editing',
                    'fashion-film-edits' => 'Fashion-film edits',
                    'colour-grading' => 'Colour grading',
                    'sound-design' => 'Sound design',
                    'highlight-reels' => 'Highlight reels',
                    'photo-retouching' => 'Photo retouching',
                    'social-short-form-edits' => 'Social short-form edits',
                    'same-day-edits' => 'Same-day edits',
                ],
            ],
            'jewellery-designer' => [
                'field_key' => 'service_jewellery_designer',
                'label' => 'Jewellery Design Specialisations',
                'options' => [
                    'bridal-jewellery-sets' => 'Bridal jewellery sets',
                    'custom-engagement-rings' => 'Custom engagement rings',
                    'temple-inspired-pieces' => 'Temple-inspired pieces',
                    'runway-accessories' => 'Runway accessories',
                    'restyling-family-gold' => 'Restyling family gold',
                    'silver-contemporary-lines' => 'Silver & contemporary lines',
                    'necklace-maang-tikka-sets' => 'Necklace & maang tikka sets',
                    'bespoke-commissions' => 'Bespoke commissions',
                ],
            ],
            'party-event-management' => [
                'field_key' => 'service_party_event_management',
                'label' => 'Event Management Services & Specialisations',
                'options' => [
                    'fashion-show-production' => 'Fashion Show & Stage Production',
                    'corporate-event-management' => 'Corporate Events & Brand Launches',
                    'luxury-wedding-planning' => 'Luxury Wedding & Sangeet Planning',
                    'theme-parties-vip-lounge' => 'Theme Parties & VIP Lounge Setup',
                    'celebrity-artist-hospitality' => 'Celebrity & Artist Hospitality',
                    'sound-light-av-production' => 'Sound, Lighting & AV Production',
                    'venue-sourcing-decor' => 'Venue Sourcing & Spatial Decor',
                    'runway-backstage-coordination' => 'Runway & Backstage Coordination',
                ],
            ],
        ];

        // Generic service field remains as template in library
        $genericField = ProfessionalFormField::where('field_key', 'service')->first();

        foreach ($specialisationsData as $catSlug => $data) {
            // 1. Create or update the field in the fields library
            $field = ProfessionalFormField::updateOrCreate(
                ['field_key' => $data['field_key']],
                [
                    'label' => $data['label'],
                    'field_type' => 'checkbox_group',
                    'help_text' => 'Select all that apply',
                    'placeholder' => null,
                    'options' => $data['options'],
                    'validation_rules' => 'required|array|min:1',
                    'is_required' => true,
                    'is_common' => false,
                    'width' => 'full',
                    'display_order' => 80,
                    'is_active' => true,
                ]
            );

            // 2. Attach to category
            $category = ProfessionalCategory::where('slug', $catSlug)->first();
            if ($catSlug === 'party-event-management' && ! $category) {
                $category = ProfessionalCategory::where('slug', 'party-event-mnagement')->first();
            }

            if ($category) {
                // Detach old generic service field if present
                if ($genericField) {
                    CategoryFormField::where('category_id', $category->id)
                        ->where('field_id', $genericField->id)
                        ->delete();
                }

                // Attach the dedicated field
                CategoryFormField::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'field_id' => $field->id,
                    ],
                    [
                        'display_order' => 80,
                        'is_required' => true,
                        'label_override' => null, // Let it inherit from field->label
                        'options_override' => null, // Let it inherit from field->options for library sync
                        'placeholder_override' => null,
                        'help_text_override' => null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe revert if needed
    }
};
