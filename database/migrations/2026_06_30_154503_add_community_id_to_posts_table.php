<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Menambahkan kolom community_id (nullable jika post tidak harus selalu masuk komunitas)
            $table->foreignId('community_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['community_id']);
            $table->dropColumn('community_id');
        });
    }
};