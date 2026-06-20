<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a category_tag column to the posts table.
     *
     * Stores exactly one of the 4 allowed forum tags:
     *   - Hiking Stories
     *   - Tips and Trick
     *   - Gear & Equipment
     *   - Safety & Survival
     *
     * nullable() allows existing rows (profile posts) to remain
     * without a category_tag without breaking anything.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('category_tag', [
                'Hiking Stories',
                'Tips and Trick',
                'Gear & Equipment',
                'Safety & Survival',
            ])->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('category_tag');
        });
    }
};
