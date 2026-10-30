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
        // 1. Seasons Table
        if (! Schema::hasTable('seasons')) {
            Schema::create('seasons', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('number')->unique();
                $table->string('title', 100);
                $table->string('year', 20)->nullable();
                $table->string('location', 100)->nullable();
                $table->string('dates', 150)->nullable();
                $table->string('status', 30)->default('published')->index(); // published, draft, archived
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Models Table
        if (! Schema::hasTable('models')) {
            Schema::create('models', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('slug', 180)->unique();
                $table->string('gender', 30)->default('Female')->index(); // Female, Male, Kids, Other
                $table->string('level', 50)->default('New Face')->index(); // New Face, Experienced, Kids
                $table->boolean('is_kid')->default(false)->index();
                $table->unsignedInteger('age')->nullable();
                $table->string('height', 50)->nullable();
                $table->string('location', 100)->nullable();
                $table->string('experience', 100)->nullable();
                $table->string('profile_photo')->nullable();
                $table->json('types')->nullable(); // Runway, Editorial, Commercial
                $table->json('measurements')->nullable(); // Bust, Waist, Hips, Shoe, Chest
                $table->json('skills')->nullable();
                $table->json('specialities')->nullable();
                $table->json('portfolio_images')->nullable();
                $table->string('availability', 150)->nullable();
                $table->text('description')->nullable();
                $table->string('instagram_url')->nullable();
                $table->string('portfolio_url')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->integer('display_order')->default(0)->index();
                $table->string('status', 30)->default('published')->index(); // published, draft, archived
                $table->timestamps();
            });
        }

        // 3. Designers Table
        if (! Schema::hasTable('designers')) {
            Schema::create('designers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('brand_name', 150)->nullable()->index();
                $table->string('slug', 180)->unique();
                $table->string('profile_photo')->nullable();
                $table->string('brand_logo')->nullable();
                $table->string('location', 100)->nullable();
                $table->string('experience', 100)->nullable();
                $table->string('short_intro', 255)->nullable();
                $table->text('biography')->nullable();
                $table->string('collection_title', 150)->nullable();
                $table->text('collection_description')->nullable();
                $table->json('services')->nullable();
                $table->json('portfolio_images')->nullable();
                $table->string('website_url')->nullable();
                $table->string('instagram_url')->nullable();
                $table->json('other_social_links')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->integer('display_order')->default(0)->index();
                $table->string('status', 30)->default('published')->index();
                $table->timestamps();
            });
        }

        // 4. Creative Professionals Table
        if (! Schema::hasTable('creative_professionals')) {
            Schema::create('creative_professionals', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('category', 60)->index(); // makeup-artist, hairstylist, photographer, videographer, etc.
                $table->string('professional_title', 150)->nullable();
                $table->string('slug', 180)->unique();
                $table->string('profile_photo')->nullable();
                $table->string('location', 100)->nullable();
                $table->string('experience', 100)->nullable();
                $table->text('biography')->nullable();
                $table->json('services')->nullable();
                $table->json('portfolio_images')->nullable();
                $table->string('website_url')->nullable();
                $table->string('instagram_url')->nullable();
                $table->json('other_social_links')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->integer('display_order')->default(0)->index();
                $table->string('status', 30)->default('published')->index();
                $table->timestamps();
            });
        }

        // 5. Pivot Tables for Season Relationships
        if (! Schema::hasTable('model_season')) {
            Schema::create('model_season', function (Blueprint $table) {
                $table->id();
                $table->foreignId('model_id')->constrained('models')->cascadeOnDelete();
                $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
                $table->unique(['model_id', 'season_id']);
            });
        }

        if (! Schema::hasTable('designer_season')) {
            Schema::create('designer_season', function (Blueprint $table) {
                $table->id();
                $table->foreignId('designer_id')->constrained('designers')->cascadeOnDelete();
                $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
                $table->unique(['designer_id', 'season_id']);
            });
        }

        if (! Schema::hasTable('creative_professional_season')) {
            Schema::create('creative_professional_season', function (Blueprint $table) {
                $table->id();
                $table->foreignId('creative_professional_id')->constrained('creative_professionals')->cascadeOnDelete();
                $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
                $table->unique(['creative_professional_id', 'season_id'], 'pro_season_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creative_professional_season');
        Schema::dropIfExists('designer_season');
        Schema::dropIfExists('model_season');
        Schema::dropIfExists('creative_professionals');
        Schema::dropIfExists('designers');
        Schema::dropIfExists('models');
        Schema::dropIfExists('seasons');
    }
};
