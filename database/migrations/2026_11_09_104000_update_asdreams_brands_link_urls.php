<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('asdreams_brands')) {
            DB::table('asdreams_brands')->where('order', 1)->orWhere('name', 'ASDreamStudio')->update(['link_url' => '/asdreams']);
            DB::table('asdreams_brands')->where('order', 2)->orWhere('name', 'IDFR')->update(['link_url' => '/idfr']);
            DB::table('asdreams_brands')->where('order', 3)->orWhere('name', "Athul's Academy")->update(['link_url' => '/asdreams/contact?brand=athuls']);
            DB::table('asdreams_brands')->where('order', 4)->orWhere('name', 'Lakme')->update(['link_url' => '/asdreams/contact?brand=lakme']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('asdreams_brands')) {
            DB::table('asdreams_brands')->where('order', 1)->orWhere('name', 'ASDreamStudio')->update(['link_url' => '/asdreams#home']);
            DB::table('asdreams_brands')->where('order', 2)->orWhere('name', 'IDFR')->update(['link_url' => '/']);
        }
    }
};
