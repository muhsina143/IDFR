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
        if (! Schema::hasColumn('asdreams_contact_enquiries', 'brand')) {
            Schema::table('asdreams_contact_enquiries', function (Blueprint $table) {
                $table->string('brand', 50)->nullable()->default('asdreams')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('asdreams_contact_enquiries', 'brand')) {
            Schema::table('asdreams_contact_enquiries', function (Blueprint $table) {
                $table->dropColumn('brand');
            });
        }
    }
};
