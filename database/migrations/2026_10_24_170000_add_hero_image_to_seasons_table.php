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
        if (Schema::hasTable('seasons') && ! Schema::hasColumn('seasons', 'hero_image')) {
            Schema::table('seasons', function (Blueprint $table) {
                $table->string('hero_image')->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seasons') && Schema::hasColumn('seasons', 'hero_image')) {
            Schema::table('seasons', function (Blueprint $table) {
                $table->dropColumn('hero_image');
            });
        }
    }
};
