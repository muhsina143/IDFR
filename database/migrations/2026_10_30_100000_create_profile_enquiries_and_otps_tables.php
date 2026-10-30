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
        if (! Schema::hasTable('profile_enquiries')) {
            Schema::create('profile_enquiries', function (Blueprint $table) {
                $table->id();
                $table->string('professional_type'); // 'model', 'creative_professional', 'designer'
                $table->unsignedBigInteger('professional_id')->nullable();
                $table->string('professional_name');
                $table->string('professional_category'); // 'Model', 'Makeup Artist', 'Fashion Designer', etc.
                $table->string('name'); // visitor name
                $table->string('email'); // visitor email
                $table->string('phone'); // visitor phone with country code
                $table->string('assignment_type'); // Runway Show, Photoshoot, etc.
                $table->date('preferred_date')->nullable();
                $table->string('location');
                $table->string('budget')->nullable();
                $table->text('brief');
                $table->boolean('is_email_verified')->default(true);
                $table->timestamp('verified_at')->nullable();
                $table->string('status')->default('new'); // new, contacted, in_progress, completed, cancelled
                $table->text('admin_notes')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['professional_type', 'professional_id']);
                $table->index('status');
                $table->index('email');
                $table->index('created_at');
            });
        }

        if (! Schema::hasTable('enquiry_otps')) {
            Schema::create('enquiry_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
                $table->string('otp_hash');
                $table->string('session_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->unsignedSmallInteger('attempts')->default(0);
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->index(['email', 'expires_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiry_otps');
        Schema::dropIfExists('profile_enquiries');
    }
};
