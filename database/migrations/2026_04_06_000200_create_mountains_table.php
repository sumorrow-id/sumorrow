<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mountains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->integer('elevation_masl');
            $table->string('coordinates');
            $table->text('description');
            $table->boolean('is_open')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('closed_since')->nullable();
            $table->float('min_length_km');
            $table->float('max_length_km');
            $table->float('min_elevation_gain_m');
            $table->float('max_elevation_gain_m');
            $table->integer('min_est_duration_minutes');
            $table->integer('max_est_duration_minutes');
            $table->enum('difficulty', ['easy', 'moderate', 'hard', 'strenuous']);
            $table->float('avg_rating')->default(0);

            $table->index('is_open');
            $table->index('is_active');
            $table->index('difficulty');
            $table->index('avg_rating');
            $table->index('elevation_masl');
        });

        //  MYSQL DB Statement
        DB::statement('ALTER TABLE mountains ADD FULLTEXT mountains_name_fulltext (name), ADD FULLTEXT mountains_description_fulltext (description)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mountains');
    }
};
