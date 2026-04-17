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
        Schema::create('basecamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mountain_id')->constrained('mountains')->cascadeOnDelete();
            $table->foreignId('regency_id')->constrained('regencies')->cascadeOnDelete();
            $table->string('name');
            $table->integer('base_elevation_masl');
            $table->float('length_km');
            $table->integer('elevation_gain_m');
            $table->integer('est_duration_minutes');

            $table->index('mountain_id');
            $table->index('regency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basecamps');
    }
};

