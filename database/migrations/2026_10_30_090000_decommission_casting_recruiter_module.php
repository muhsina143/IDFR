<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to decommission Casting Recruiter module safely.
     */
    public function up(): void
    {
        // 1. Update About statistics: replace "Casting Recruiters" with "Creative Talents"
        if (Schema::hasTable('about_statistics')) {
            DB::table('about_statistics')
                ->where('title', 'Casting Recruiters')
                ->update([
                    'title' => 'Creative Talents',
                    'updated_at' => now(),
                ]);
        }

        // 2. Safe User role update: migrate existing 'recruiter' accounts to general 'member' role
        // This preserves all shared user records, authentication credentials, and data safely.
        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('role', 'recruiter')
                ->update([
                    'role' => 'member',
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('about_statistics')) {
            DB::table('about_statistics')
                ->where('title', 'Creative Talents')
                ->update([
                    'title' => 'Casting Recruiters',
                    'updated_at' => now(),
                ]);
        }

        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('role', 'member')
                ->update([
                    'role' => 'recruiter',
                    'updated_at' => now(),
                ]);
        }
    }
};
