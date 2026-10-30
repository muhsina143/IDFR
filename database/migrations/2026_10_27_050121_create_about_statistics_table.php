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
        if (! Schema::hasTable('about_statistics')) {
            Schema::create('about_statistics', function (Blueprint $table) {
                $table->id();
                $table->string('value', 50);
                $table->string('title', 150);
                $table->string('prefix', 20)->nullable();
                $table->string('suffix', 20)->nullable();
                $table->integer('display_order')->default(0);
                $table->string('status', 20)->default('active'); // active, inactive
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['status', 'display_order']);
                $table->index(['is_active', 'display_order']);
            });
        }

        // Initial 4 records from existing About page statistics
        $initialStats = [
            [
                'title' => 'Seasons',
                'value' => '04',
                'prefix' => null,
                'suffix' => null,
                'display_order' => 1,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'title' => 'Registered Models',
                'value' => '1200',
                'prefix' => null,
                'suffix' => '+',
                'display_order' => 2,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'title' => 'Casting Recruiters',
                'value' => '350',
                'prefix' => null,
                'suffix' => '+',
                'display_order' => 3,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'title' => 'Designers Showcased',
                'value' => '85',
                'prefix' => null,
                'suffix' => '+',
                'display_order' => 4,
                'status' => 'active',
                'is_active' => true,
            ],
        ];

        $now = now();
        foreach ($initialStats as $stat) {
            DB::table('about_statistics')->updateOrInsert(
                ['title' => $stat['title']],
                array_merge($stat, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_statistics');
    }
};
