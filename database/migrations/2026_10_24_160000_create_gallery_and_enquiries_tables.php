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
        // 1. Gallery Images Table
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->string('season_name', 50)->default('Season 1');
            $table->string('title', 255);
            $table->string('alt_text', 255)->nullable();
            $table->string('image_path', 255);
            $table->integer('width')->default(1000);
            $table->integer('height')->default(1500);
            $table->integer('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->string('status', 20)->default('published'); // published, draft, archived
            $table->timestamps();

            $table->index(['status', 'display_order']);
            $table->index('season_name');
        });

        // 2. Contact Enquiries Table
        Schema::create('contact_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 30);
            $table->string('email', 150)->nullable();
            $table->string('subject', 200)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('unread'); // unread, read, responded, archived
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_enquiries');
        Schema::dropIfExists('gallery_images');
    }
};
