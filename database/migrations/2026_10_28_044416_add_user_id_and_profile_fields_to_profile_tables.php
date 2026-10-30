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
        // 1. Models Table
        if (Schema::hasTable('models')) {
            Schema::table('models', function (Blueprint $table) {
                if (! Schema::hasColumn('models', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('models', 'phone')) {
                    $table->string('phone', 50)->nullable()->after('location');
                }
                if (! Schema::hasColumn('models', 'email')) {
                    $table->string('email', 150)->nullable()->after('phone');
                }
                if (! Schema::hasColumn('models', 'outstation')) {
                    $table->string('outstation', 20)->nullable()->after('availability');
                }
                if (! Schema::hasColumn('models', 'international')) {
                    $table->string('international', 20)->nullable()->after('outstation');
                }
            });
        }

        // 2. Designers Table
        if (Schema::hasTable('designers')) {
            Schema::table('designers', function (Blueprint $table) {
                if (! Schema::hasColumn('designers', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('designers', 'phone')) {
                    $table->string('phone', 50)->nullable()->after('location');
                }
                if (! Schema::hasColumn('designers', 'email')) {
                    $table->string('email', 150)->nullable()->after('phone');
                }
                if (! Schema::hasColumn('designers', 'atelier_notes')) {
                    $table->text('atelier_notes')->nullable()->after('collection_description');
                }
                if (! Schema::hasColumn('designers', 'outstation')) {
                    $table->string('outstation', 20)->nullable()->after('services');
                }
            });
        }

        // 3. Creative Professionals Table
        if (Schema::hasTable('creative_professionals')) {
            Schema::table('creative_professionals', function (Blueprint $table) {
                if (! Schema::hasColumn('creative_professionals', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('creative_professionals', 'brand')) {
                    $table->string('brand', 100)->nullable()->after('category');
                }
                if (! Schema::hasColumn('creative_professionals', 'phone')) {
                    $table->string('phone', 50)->nullable()->after('location');
                }
                if (! Schema::hasColumn('creative_professionals', 'email')) {
                    $table->string('email', 150)->nullable()->after('phone');
                }
                if (! Schema::hasColumn('creative_professionals', 'equipment')) {
                    $table->text('equipment')->nullable()->after('biography');
                }
                if (! Schema::hasColumn('creative_professionals', 'outstation')) {
                    $table->string('outstation', 20)->nullable()->after('services');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('models')) {
            Schema::table('models', function (Blueprint $table) {
                if (Schema::hasColumn('models', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
                $dropColumns = array_filter(['phone', 'email', 'outstation', 'international'], fn ($c) => Schema::hasColumn('models', $c));
                if (! empty($dropColumns)) {
                    $table->dropColumn($dropColumns);
                }
            });
        }

        if (Schema::hasTable('designers')) {
            Schema::table('designers', function (Blueprint $table) {
                if (Schema::hasColumn('designers', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
                $dropColumns = array_filter(['phone', 'email', 'atelier_notes', 'outstation'], fn ($c) => Schema::hasColumn('designers', $c));
                if (! empty($dropColumns)) {
                    $table->dropColumn($dropColumns);
                }
            });
        }

        if (Schema::hasTable('creative_professionals')) {
            Schema::table('creative_professionals', function (Blueprint $table) {
                if (Schema::hasColumn('creative_professionals', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
                $dropColumns = array_filter(['brand', 'phone', 'email', 'equipment', 'outstation'], fn ($c) => Schema::hasColumn('creative_professionals', $c));
                if (! empty($dropColumns)) {
                    $table->dropColumn($dropColumns);
                }
            });
        }
    }
};
