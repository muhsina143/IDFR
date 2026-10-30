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
        if (Schema::hasColumn('profile_enquiries', 'budget')) {
            Schema::table('profile_enquiries', function (Blueprint $table) {
                $table->dropColumn('budget');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('profile_enquiries', 'budget')) {
            Schema::table('profile_enquiries', function (Blueprint $table) {
                $table->string('budget')->nullable()->after('location');
            });
        }
    }
};

