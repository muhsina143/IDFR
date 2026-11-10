<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Offers
        Schema::create('asdreams_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category_label')->nullable();
            $table->string('heading_main');
            $table->string('heading_highlight')->nullable();
            $table->text('lead_text')->nullable();
            $table->longText('full_description')->nullable();
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->text('enquiry_placeholder')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default('published');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        // 2. Offer features (repeatable list)
        Schema::create('asdreams_offer_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('asdreams_offers')->cascadeOnDelete();
            $table->text('feature_text');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. Offer additional gallery images
        Schema::create('asdreams_offer_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('asdreams_offers')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 4. Banners / Hero slides
        Schema::create('asdreams_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('highlight_title')->nullable();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('alt_text')->nullable();
            $table->string('button_text')->default('Offer Details');
            $table->unsignedBigInteger('linked_offer_id')->nullable();
            $table->string('linked_offer_slug')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('linked_offer_id')->references('id')->on('asdreams_offers')->nullOnDelete();
        });

        // 5. Brands
        Schema::create('asdreams_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path');
            $table->string('cover_image')->nullable();
            $table->string('link_url')->nullable();
            $table->json('tags')->nullable();
            $table->string('css_variant')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. About Page Content
        Schema::create('asdreams_about_contents', function (Blueprint $table) {
            $table->id();
            $table->string('banner_image')->nullable();
            $table->string('title')->default('About Us');
            $table->string('video_cover_image')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('years_of_experience')->default(32);
            $table->string('how_we_work_title')->default('How we work?');
            $table->text('how_we_work_text')->nullable();
            $table->string('story_heading_main')->default('Behind the Lens,');
            $table->string('story_heading_highlight')->default('Ahead of the Curve');
            $table->text('story_paragraph_1')->nullable();
            $table->json('story_bullets')->nullable();
            $table->text('story_extended_text')->nullable();
            $table->string('studio_image')->nullable();
            $table->string('studio_caption_title')->default('Studio 01');
            $table->string('studio_caption_subtitle')->default('Kozhikode, Kerala');
            $table->timestamps();
        });

        // 7. Gallery Categories
        Schema::create('asdreams_gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tab_label')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. Gallery Images
        Schema::create('asdreams_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('asdreams_gallery_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('image_path');
            $table->integer('width')->default(1000);
            $table->integer('height')->default(1500);
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 9. Team Members
        Schema::create('asdreams_team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation');
            $table->text('bio')->nullable();
            $table->string('photo_path');
            $table->json('social_links')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 10. Contact Settings
        Schema::create('asdreams_contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section_label')->default('Get in Touch');
            $table->string('title')->default('Let’s create');
            $table->string('title_highlight')->default('together');
            $table->text('intro')->nullable();
            $table->text('studio_address')->nullable();
            $table->string('maps_url')->nullable();
            $table->string('studio_phone')->nullable();
            $table->string('bookings_phone')->nullable();
            $table->string('general_email')->nullable();
            $table->string('bookings_email')->nullable();
            $table->json('working_hours')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // 11. Offer Enquiries
        Schema::create('asdreams_offer_enquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->string('offer_title')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->date('preferred_date')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('offer_id')->references('id')->on('asdreams_offers')->nullOnDelete();
        });

        // 12. Contact Enquiries
        Schema::create('asdreams_contact_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 50)->nullable()->default('asdreams');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // 13. Shoot Bookings
        Schema::create('asdreams_shoot_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->date('shoot_date')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('shoot_type')->nullable();
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // 14. Website Settings
        Schema::create('asdreams_website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asdreams_website_settings');
        Schema::dropIfExists('asdreams_shoot_bookings');
        Schema::dropIfExists('asdreams_contact_enquiries');
        Schema::dropIfExists('asdreams_offer_enquiries');
        Schema::dropIfExists('asdreams_contact_settings');
        Schema::dropIfExists('asdreams_team_members');
        Schema::dropIfExists('asdreams_gallery_images');
        Schema::dropIfExists('asdreams_gallery_categories');
        Schema::dropIfExists('asdreams_about_contents');
        Schema::dropIfExists('asdreams_brands');
        Schema::dropIfExists('asdreams_banners');
        Schema::dropIfExists('asdreams_offer_images');
        Schema::dropIfExists('asdreams_offer_features');
        Schema::dropIfExists('asdreams_offers');
    }
};
