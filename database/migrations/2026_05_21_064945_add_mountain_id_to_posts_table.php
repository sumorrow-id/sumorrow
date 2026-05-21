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
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('mountain_id')->nullable()->constrained('mountains')->nullOnDelete();
            $table->date('climbing_date')->nullable();
            $table->integer('duration_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['mountain_id']);
            $table->dropColumn(['mountain_id', 'climbing_date', 'duration_days']);
        });
    }
};
