<?php

use App\Models\CategoryFormField;
use App\Models\CreativeProfessional;
use App\Models\ProfessionalCategory;
use App\Models\ProfessionalFormField;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('professional_categories') || ! Schema::hasTable('category_form_fields')) {
            return;
        }

        // 1. Locate Party & Event Management category (either misspelled or correct slug)
        $category = ProfessionalCategory::whereIn('slug', ['party-event-management', 'party-event-mnagement'])
            ->orWhere('name', 'like', '%Party%')
            ->first();

        $partyServices = [
            'fashion-show-production' => 'Fashion Show & Stage Production',
            'corporate-event-management' => 'Corporate Events & Brand Launches',
            'luxury-wedding-planning' => 'Luxury Wedding & Sangeet Planning',
            'theme-parties-vip-lounge' => 'Theme Parties & VIP Lounge Setup',
            'celebrity-artist-hospitality' => 'Celebrity & Artist Hospitality',
            'sound-light-av-production' => 'Sound, Lighting & AV Production',
            'venue-sourcing-decor' => 'Venue Sourcing & Spatial Decor',
            'runway-backstage-coordination' => 'Runway & Backstage Coordination',
        ];

        if ($category) {
            $category->update([
                'name' => 'Party & Event Management',
                'slug' => 'party-event-management',
                'hero_title' => 'Party & <em>Event Management</em>',
                'description' => 'Complete event architecture, luxury wedding planning, stage production, sound & lighting engineering, and runway show management.',
                'form_title' => 'Party & Event Management Details',
                'form_lead' => 'Fill in your event management expertise, specialisations and contact details to publish your creative profile.',
                'is_active' => true,
            ]);
        } else {
            $category = ProfessionalCategory::create([
                'name' => 'Party & Event Management',
                'slug' => 'party-event-management',
                'hero_title' => 'Party & <em>Event Management</em>',
                'description' => 'Complete event architecture, luxury wedding planning, stage production, sound & lighting engineering, and runway show management.',
                'form_title' => 'Party & Event Management Details',
                'form_lead' => 'Fill in your event management expertise, specialisations and contact details to publish your creative profile.',
                'display_order' => 90,
                'is_active' => true,
            ]);
        }

        // 2. Ensure the service field exists and configure options on the pivot
        $serviceField = ProfessionalFormField::where('field_key', 'service')->first();
        if ($serviceField) {
            $pivot = CategoryFormField::where('category_id', $category->id)
                ->where('field_id', $serviceField->id)
                ->first();

            if ($pivot) {
                $pivot->update([
                    'label_override' => 'Event Management Services & Specialisations',
                    'options_override' => $partyServices,
                    'is_required' => true,
                    'is_active' => true,
                ]);
            } else {
                CategoryFormField::create([
                    'category_id' => $category->id,
                    'field_id' => $serviceField->id,
                    'label_override' => 'Event Management Services & Specialisations',
                    'options_override' => $partyServices,
                    'display_order' => 80,
                    'is_required' => true,
                    'is_active' => true,
                ]);
            }
        }

        // 3. Update existing users and creative professionals to standard slug
        User::where('role', 'party-event-mnagement')->update(['role' => 'party-event-management']);
        CreativeProfessional::where('category', 'party-event-mnagement')->update(['category' => 'party-event-management']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe reversible no-op
    }
};
