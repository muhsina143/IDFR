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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'email_otp_hash')) {
                $table->string('email_otp_hash', 255)->nullable()->after('email_verified_at');
            }
            if (! Schema::hasColumn('users', 'email_otp_expires_at')) {
                $table->timestamp('email_otp_expires_at')->nullable()->after('email_otp_hash');
            }
            if (! Schema::hasColumn('users', 'email_otp_sent_at')) {
                $table->timestamp('email_otp_sent_at')->nullable()->after('email_otp_expires_at');
            }
            if (! Schema::hasColumn('users', 'email_otp_attempts')) {
                $table->unsignedTinyInteger('email_otp_attempts')->default(0)->after('email_otp_sent_at');
            }
            if (! Schema::hasColumn('users', 'payment_status')) {
                $table->string('payment_status', 30)->default('exempt')->after('role')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['phone', 'email_otp_hash', 'email_otp_expires_at', 'email_otp_sent_at', 'email_otp_attempts', 'payment_status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
