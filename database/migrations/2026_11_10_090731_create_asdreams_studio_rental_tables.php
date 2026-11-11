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
        Schema::create('asdreams_studio_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon', 50)->nullable()->default('check');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('asdreams_studio_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon', 50)->nullable()->default('light');
            $table->string('image_path')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('asdreams_studio_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asdreams_studio_gallery_images');
        Schema::dropIfExists('asdreams_studio_equipments');
        Schema::dropIfExists('asdreams_studio_facilities');
    }
};
