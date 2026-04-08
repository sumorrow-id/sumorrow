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
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->foreignUuid('village_id')->constrained('villages')->cascadeOnDelete();
            $table->integer('elevation_masl');
            $table->string('coordinates');
            $table->text('description');
            $table->boolean('is_open')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('closed_since')->nullable();
            $table->float('length_km');
            $table->float('elevation_gain_m');
            $table->float('est_duration_minutes');
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
