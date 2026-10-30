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
        Schema::create('casting_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('model_id')->constrained('models')->cascadeOnDelete();
            $table->string('recruiter_name');
            $table->string('recruiter_email');
            $table->string('recruiter_phone');
            $table->string('project_name');
            $table->text('casting_requirements');
            $table->text('project_description');
            $table->date('shoot_date')->nullable();
            $table->string('location');
            $table->text('additional_message')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['model_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casting_inquiries');
    }
};
