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
        Schema::create('mountains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('region_id')->constrained('administrative_regions')->cascadeOnDelete();
            $table->integer('elevation_masl');
            $table->string('coordinates');
            $table->text('description');
            $table->string('image_url');
            $table->boolean('is_open')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('closed_since')->nullable();
            $table->float('avg_rating')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mountains');
    }
};

