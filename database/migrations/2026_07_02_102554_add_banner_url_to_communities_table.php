<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('communities', function (Blueprint $table) {
            // Gunakan after('image_url') agar rapi di database
            $table->string('banner_url')->nullable()->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropColumn('banner_url');
        });
    }
};
