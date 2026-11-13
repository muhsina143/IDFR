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
        Schema::table('asdreams_team_members', function (Blueprint $table): void {
            if (! Schema::hasColumn('asdreams_team_members', 'member_type')) {
                $table->string('member_type', 30)->default('team_member')->after('id');
            }

            if (! Schema::hasColumn('asdreams_team_members', 'designation')) {
                $table->string('designation')->nullable()->after('name');
            } else {
                $table->string('designation')->nullable()->change();
            }

            if (! Schema::hasColumn('asdreams_team_members', 'bio')) {
                $table->text('bio')->nullable()->after('designation');
            }

            if (! Schema::hasColumn('asdreams_team_members', 'display_order')) {
                $table->integer('display_order')->default(0)->after('social_links');
            }

            if (! Schema::hasColumn('asdreams_team_members', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('display_order');
            }
        });

        // Backfill existing team members safely across MySQL and SQLite.
        $hasOrder = Schema::hasColumn('asdreams_team_members', 'order');
        $hasPublished = Schema::hasColumn('asdreams_team_members', 'is_published');

        $selectColumns = ['id'];
        if ($hasOrder) {
            $selectColumns[] = 'order';
        }
        if ($hasPublished) {
            $selectColumns[] = 'is_published';
        }

        DB::table('asdreams_team_members')
            ->select($selectColumns)
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($hasOrder, $hasPublished): void {
                foreach ($rows as $row) {
                    $update = [
                        'member_type' => 'team_member',
                    ];

                    if ($hasOrder) {
                        $update['display_order'] = (int) ($row->order ?? 0);
                    }

                    if ($hasPublished) {
                        $update['is_active'] = (bool) $row->is_published;
                    }

                    DB::table('asdreams_team_members')
                        ->where('id', $row->id)
                        ->update($update);
                }
            });

        if (! Schema::hasIndex('asdreams_team_members', 'asd_team_type_active_order_idx')) {
            Schema::table('asdreams_team_members', function (Blueprint $table): void {
                $table->index(['member_type', 'is_active', 'display_order'], 'asd_team_type_active_order_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('asdreams_team_members', 'asd_team_type_active_order_idx')) {
            Schema::table('asdreams_team_members', function (Blueprint $table): void {
                $table->dropIndex('asd_team_type_active_order_idx');
            });
        }

        Schema::table('asdreams_team_members', function (Blueprint $table): void {
            $columnsToDrop = [];

            foreach (['is_active', 'display_order', 'member_type'] as $column) {
                if (Schema::hasColumn('asdreams_team_members', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
