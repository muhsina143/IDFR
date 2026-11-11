<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables tracking ASDreams enquiry and booking unread state.
     *
     * @var list<string>
     */
    private array $tables = [
        'asdreams_offer_enquiries',
        'asdreams_contact_enquiries',
        'asdreams_shoot_bookings',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'read_at')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->timestamp('read_at')->nullable()->after('status');
                });

                DB::table($tableName)
                    ->where('status', '!=', 'new')
                    ->whereNull('read_at')
                    ->update(['read_at' => DB::raw('COALESCE(updated_at, created_at, CURRENT_TIMESTAMP)')]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'read_at')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('read_at');
                });
            }
        }
    }
};
