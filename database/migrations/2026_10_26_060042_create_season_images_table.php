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
        Schema::create('season_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->string('image_path', 255);
            $table->string('image_title', 150)->nullable();
            $table->string('caption', 255)->nullable();
            $table->string('designer_name', 150)->nullable();
            $table->string('look_number', 50)->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['season_id', 'is_active', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season_images');
    }
};
