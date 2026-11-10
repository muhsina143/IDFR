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
        Schema::table('asdreams_offers', function (Blueprint $table) {
            if (! Schema::hasColumn('asdreams_offers', 'banner_id')) {
                $table->unsignedBigInteger('banner_id')->nullable()->after('id');
                $table->foreign('banner_id')->references('id')->on('asdreams_banners')->nullOnDelete();
            }
            if (! Schema::hasColumn('asdreams_offers', 'price')) {
                $table->string('price', 100)->nullable()->after('image');
            }
            if (! Schema::hasColumn('asdreams_offers', 'validity')) {
                $table->string('validity', 150)->nullable()->after('price');
            }
            if (! Schema::hasColumn('asdreams_offers', 'terms')) {
                $table->text('terms')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('asdreams_offers', 'important_notes')) {
                $table->text('important_notes')->nullable()->after('terms');
            }
        });

        Schema::table('asdreams_offer_enquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('asdreams_offer_enquiries', 'banner_id')) {
                $table->unsignedBigInteger('banner_id')->nullable()->after('id');
                $table->foreign('banner_id')->references('id')->on('asdreams_banners')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asdreams_offers', function (Blueprint $table) {
            if (Schema::hasColumn('asdreams_offers', 'banner_id')) {
                $table->dropForeign(['banner_id']);
                $table->dropColumn('banner_id');
            }
            if (Schema::hasColumn('asdreams_offers', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('asdreams_offers', 'validity')) {
                $table->dropColumn('validity');
            }
            if (Schema::hasColumn('asdreams_offers', 'terms')) {
                $table->dropColumn('terms');
            }
            if (Schema::hasColumn('asdreams_offers', 'important_notes')) {
                $table->dropColumn('important_notes');
            }
        });

        Schema::table('asdreams_offer_enquiries', function (Blueprint $table) {
            if (Schema::hasColumn('asdreams_offer_enquiries', 'banner_id')) {
                $table->dropForeign(['banner_id']);
                $table->dropColumn('banner_id');
            }
        });
    }
};
