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
        Schema::create('asdreams_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('logo_style')->nullable();
            $table->string('logo_mark')->nullable();
            $table->string('logo_word')->nullable();
            $table->string('logo_sub')->nullable();
            $table->string('featured_image')->nullable();
            $table->text('description')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_role')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('asdreams_moving_client_logos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('logo_style')->nullable();
            $table->string('logo_mark')->nullable();
            $table->string('logo_word')->nullable();
            $table->string('logo_sub')->nullable();
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
        Schema::dropIfExists('asdreams_moving_client_logos');
        Schema::dropIfExists('asdreams_clients');
    }
};
