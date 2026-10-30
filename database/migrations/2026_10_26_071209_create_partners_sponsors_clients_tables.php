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
        // 1. Partners Table
        if (! Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('type', 100)->default('Partner'); // Photography Partner, Couture Partner, etc.
                $table->text('description')->nullable();
                $table->string('image_path', 255)->nullable(); // Media/photo for card
                $table->string('logo_path', 255)->nullable();  // Custom uploaded logo
                $table->string('logo_style', 50)->nullable(); // circle, diamond, crest, rings, bars, star, etc.
                $table->string('logo_mark', 20)->nullable();  // Monogram letter
                $table->string('logo_word', 100)->nullable(); // Word line
                $table->string('logo_sub', 100)->nullable();  // Sub line
                $table->string('website_url', 255)->nullable();
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'display_order']);
            });
        }

        // 2. Sponsors Table
        if (! Schema::hasTable('sponsors')) {
            Schema::create('sponsors', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('tier', 100)->default('Sponsor'); // Title Sponsor, Automobile Partner, etc.
                $table->boolean('is_title')->default(false);
                $table->text('description')->nullable();
                $table->string('image_path', 255)->nullable();
                $table->string('logo_path', 255)->nullable();
                $table->string('logo_style', 50)->nullable();
                $table->string('logo_mark', 20)->nullable();
                $table->string('logo_word', 100)->nullable();
                $table->string('logo_sub', 100)->nullable();
                $table->string('website_url', 255)->nullable();
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'display_order']);
                $table->index('is_title');
            });
        }

        // 3. Clients & Testimonials Table
        if (! Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('industry', 100)->nullable(); // Couture House, Bridal Wear, etc.
                $table->string('image_path', 255)->nullable(); // Featured testimonial / campaign image
                $table->string('logo_path', 255)->nullable();  // Custom logo
                $table->string('logo_style', 50)->nullable();
                $table->string('logo_mark', 20)->nullable();
                $table->string('logo_word', 100)->nullable();
                $table->string('logo_sub', 100)->nullable();
                $table->text('testimonial_quote')->nullable();
                $table->string('author_name', 150)->nullable();
                $table->string('author_role', 150)->nullable(); // Designation: Creative Director, Founder, etc.
                $table->string('company_name', 150)->nullable();
                $table->string('website_url', 255)->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('include_in_strip')->default(true);
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'display_order']);
                $table->index('is_featured');
            });
        }

        // 4. Moving Client Logos Table
        if (! Schema::hasTable('moving_logos')) {
            Schema::create('moving_logos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
                $table->foreignId('sponsor_id')->nullable()->constrained('sponsors')->nullOnDelete();
                $table->string('company_name', 150);
                $table->string('logo_path', 255)->nullable();
                $table->string('logo_style', 50)->nullable();
                $table->string('logo_mark', 20)->nullable();
                $table->string('logo_word', 100)->nullable();
                $table->string('logo_sub', 100)->nullable();
                $table->string('website_url', 255)->nullable();
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'display_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moving_logos');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('partners');
    }
};
