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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['regency', 'city']);

            $table->unique(['province_id', 'name']);
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regency_id')->constrained('regencies')->cascadeOnDelete();
            $table->string('name');

            $table->unique(['regency_id', 'name']);
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('name');

            $table->unique(['district_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};

