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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('payment_status')->index();
            }
            if (! Schema::hasColumn('users', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('users', 'deactivation_reason')) {
                $table->string('deactivation_reason', 255)->nullable()->after('deactivated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['is_active', 'deactivated_at', 'deactivation_reason'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
