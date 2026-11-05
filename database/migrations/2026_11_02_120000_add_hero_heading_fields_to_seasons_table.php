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
        if (Schema::hasTable('seasons')) {
            Schema::table('seasons', function (Blueprint $table) {
                if (! Schema::hasColumn('seasons', 'hero_heading')) {
                    $table->string('hero_heading', 150)->nullable()->after('title');
                }
                if (! Schema::hasColumn('seasons', 'hero_highlighted')) {
                    $table->string('hero_highlighted', 150)->nullable()->after('hero_heading');
                }
            });

            // Populate existing rows with default hero heading values if null
            DB::table('seasons')->whereNull('hero_heading')->update([
                'hero_heading' => 'The Runway',
            ]);
            DB::table('seasons')->whereNull('hero_highlighted')->update([
                'hero_highlighted' => 'Returns',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seasons')) {
            Schema::table('seasons', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('seasons', 'hero_heading')) {
                    $columns[] = 'hero_heading';
                }
                if (Schema::hasColumn('seasons', 'hero_highlighted')) {
                    $columns[] = 'hero_highlighted';
                }
                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
