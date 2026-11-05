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
        // 1. Professional Categories Table
        if (! Schema::hasTable('professional_categories')) {
            Schema::create('professional_categories', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique(); // e.g. 'makeup-artist'
                $table->string('name'); // e.g. 'Makeup Artist'
                $table->string('hero_title')->nullable(); // e.g. 'Makeup <em>Artist</em>'
                $table->text('description')->nullable();
                $table->string('form_title')->nullable(); // e.g. 'Makeup Artist Details'
                $table->string('form_lead')->nullable();
                $table->string('default_photo')->nullable();
                $table->unsignedInteger('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Dynamic Form Fields Table
        if (! Schema::hasTable('professional_form_fields')) {
            Schema::create('professional_form_fields', function (Blueprint $table) {
                $table->id();
                $table->string('field_key')->unique(); // e.g. 'name', 'place', 'experience', 'kit', etc.
                $table->string('label'); // e.g. 'Full Name', 'Location', 'Professional Kit'
                $table->string('field_type')->default('text'); // 'text', 'number', 'tel', 'email', 'combobox', 'textarea', 'checkbox_group', 'radio_group', 'select'
                $table->string('placeholder')->nullable();
                $table->string('help_text')->nullable();
                $table->string('default_value')->nullable();
                $table->json('options')->nullable(); // For select, radio, checkbox options
                $table->string('validation_rules')->nullable(); // e.g. 'required|string|min:2'
                $table->boolean('is_required')->default(true);
                $table->boolean('is_common')->default(false); // True for fields shared by all professions
                $table->string('width')->default('half'); // 'half' (col-6) or 'full' (col-12)
                $table->unsignedInteger('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Category - Form Fields Pivot Configuration Table
        if (! Schema::hasTable('category_form_fields')) {
            Schema::create('category_form_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('professional_categories')->cascadeOnDelete();
                $table->foreignId('field_id')->constrained('professional_form_fields')->cascadeOnDelete();
                $table->unsignedInteger('display_order')->default(0);
                $table->boolean('is_required')->nullable(); // Nullable override
                $table->string('label_override')->nullable();
                $table->string('placeholder_override')->nullable();
                $table->json('options_override')->nullable(); // Overrides options (e.g. category-specific services)
                $table->string('help_text_override')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['category_id', 'field_id']);
            });
        }

        // 4. Add custom_fields to creative_professionals if missing
        if (Schema::hasTable('creative_professionals') && ! Schema::hasColumn('creative_professionals', 'custom_fields')) {
            Schema::table('creative_professionals', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('portfolio_images');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('creative_professionals') && Schema::hasColumn('creative_professionals', 'custom_fields')) {
            Schema::table('creative_professionals', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }

        Schema::dropIfExists('category_form_fields');
        Schema::dropIfExists('professional_form_fields');
        Schema::dropIfExists('professional_categories');
    }
};
