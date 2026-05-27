<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mountains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->string('name');
            $table->integer('elevation_masl');
            $table->float('length_km');
            $table->integer('elevation_gain_m');
            $table->string('coordinates');
            $table->text('description');
            $table->boolean('is_active')->default(false);
            $table->date('closed_since')->nullable();
            $table->enum('difficulty', ['easy', 'moderate', 'hard', 'strenuous']);
            $table->float('avg_rating')->default(0);

            $table->index('province_id');
            $table->index('is_active');
            $table->index('difficulty');
            $table->index('avg_rating');
            $table->index('elevation_masl');
        });

        //  MYSQL DB Statement
        DB::statement('ALTER TABLE mountains ADD FULLTEXT mountains_name_fulltext (name)');
        DB::statement('ALTER TABLE mountains ADD FULLTEXT mountains_description_fulltext (description)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mountains');
    }
};
