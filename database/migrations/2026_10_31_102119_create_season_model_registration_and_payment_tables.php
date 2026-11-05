<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Season Registration Prices
        if (! Schema::hasTable('season_registration_prices')) {
            Schema::create('season_registration_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
                $table->decimal('price', 10, 2)->default(9999.00);
                $table->string('currency', 10)->default('INR');
                $table->boolean('is_active')->default(true);
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->unique('season_id');
                $table->index(['season_id', 'is_active']);
            });
        }

        // 2. Season Model Registrations
        if (! Schema::hasTable('season_model_registrations')) {
            Schema::create('season_model_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('registration_number', 50)->unique();
                $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
                $table->string('full_name', 150);
                $table->string('whatsapp_number', 50);
                $table->string('email', 150)->nullable();
                $table->unsignedInteger('age');
                $table->string('gender', 30); // male, female, other
                $table->string('category', 30)->default('adult'); // adult, kids
                $table->string('citizenship', 100);
                $table->string('parent_name', 150)->nullable();
                $table->string('parent_contact', 50)->nullable();
                $table->string('parent_relationship', 50)->nullable();
                $table->text('additional_info')->nullable();
                $table->decimal('registration_fee', 10, 2)->default(9999.00);
                $table->decimal('amount_paid', 10, 2)->default(0.00);
                $table->string('payment_status', 30)->default('unpaid'); // unpaid, pending, paid, failed, refunded
                $table->string('registration_status', 30)->default('pending'); // pending, approved, rejected, cancelled
                $table->text('admin_notes')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamps();

                $table->index(['season_id', 'created_at']);
                $table->index('payment_status');
                $table->index('registration_status');
                $table->index('category');
                $table->index('gender');
                $table->index('whatsapp_number');
            });
        }

        // 3. Model Registration Payments
        if (! Schema::hasTable('model_registration_payments')) {
            Schema::create('model_registration_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('season_model_registration_id')->constrained('season_model_registrations')->cascadeOnDelete();
                $table->string('payment_reference', 50)->unique();
                $table->decimal('amount', 10, 2);
                $table->string('payment_method', 50)->default('cash'); // cash, bank_transfer, upi, card, cheque, other
                $table->string('transaction_reference', 100)->nullable();
                $table->dateTime('payment_date');
                $table->string('payment_status', 30)->default('paid'); // paid, pending, failed, refunded
                $table->text('notes')->nullable();
                $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['season_model_registration_id', 'payment_date'], 'reg_payments_date_idx');
                $table->index('payment_status');
            });
        }

        // 4. Seed default initial price of 9999.00 for existing seasons
        if (Schema::hasTable('seasons') && Schema::hasTable('season_registration_prices')) {
            $seasons = DB::table('seasons')->get();
            foreach ($seasons as $season) {
                $exists = DB::table('season_registration_prices')->where('season_id', $season->id)->exists();
                if (! $exists) {
                    DB::table('season_registration_prices')->insert([
                        'season_id' => $season->id,
                        'price' => 9999.00,
                        'currency' => 'INR',
                        'is_active' => true,
                        'notes' => 'Default season registration fee',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_registration_payments');
        Schema::dropIfExists('season_model_registrations');
        Schema::dropIfExists('season_registration_prices');
    }
};
