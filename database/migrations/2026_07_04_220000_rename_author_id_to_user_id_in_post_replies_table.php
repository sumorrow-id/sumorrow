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
        // Databases created while create_post_replies_table briefly used
        // user_id directly already have the target column.
        if (! Schema::hasColumn('post_replies', 'author_id')) {
            return;
        }

        Schema::table('post_replies', function (Blueprint $table) {
            $table->renameColumn('author_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_replies', function (Blueprint $table) {
            $table->renameColumn('user_id', 'author_id');
        });
    }
};
