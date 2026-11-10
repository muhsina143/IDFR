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
        Schema::table('asdreams_shoot_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('asdreams_shoot_bookings', 'booking_type')) {
                $table->string('booking_type', 30)->default('shoot')->after('shoot_type');
            }
            if (! Schema::hasColumn('asdreams_shoot_bookings', 'purpose')) {
                $table->string('purpose')->nullable()->after('booking_type');
            }
            if (! Schema::hasColumn('asdreams_shoot_bookings', 'days')) {
                $table->unsignedSmallInteger('days')->nullable()->after('purpose');
            }
            if (! Schema::hasColumn('asdreams_shoot_bookings', 'start_date')) {
                $table->date('start_date')->nullable()->after('days');
            }
            if (! Schema::hasColumn('asdreams_shoot_bookings', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asdreams_shoot_bookings', function (Blueprint $table) {
            $table->dropColumn(['booking_type', 'purpose', 'days', 'start_date', 'end_date']);
        });
    }
};
