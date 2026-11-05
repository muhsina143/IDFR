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
        if (Schema::hasTable('seasons') && ! Schema::hasColumn('seasons', 'is_current')) {
            Schema::table('seasons', function (Blueprint $table) {
                $table->boolean('is_current')->default(false)->after('status')->index();
            });

            // Set Season 8 as the currently active season
            $hasSeason8 = DB::table('seasons')->where('number', 8)->exists();
            if ($hasSeason8) {
                DB::table('seasons')->where('number', 8)->update(['is_current' => true]);
            } else {
                // Fallback to highest numbered published season
                $latest = DB::table('seasons')
                    ->where('status', 'published')
                    ->orderByDesc('number')
                    ->first();
                if ($latest) {
                    DB::table('seasons')->where('id', $latest->id)->update(['is_current' => true]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seasons') && Schema::hasColumn('seasons', 'is_current')) {
            Schema::table('seasons', function (Blueprint $table) {
                $table->dropColumn('is_current');
            });
        }
    }
};
