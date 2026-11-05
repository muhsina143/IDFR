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
        Schema::table('professional_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('professional_categories', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('default_photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_categories', function (Blueprint $table) {
            if (Schema::hasColumn('professional_categories', 'banner_image')) {
                $table->dropColumn('banner_image');
            }
        });
    }
};
