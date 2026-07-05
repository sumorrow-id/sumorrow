<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->string('join_token', 16)->nullable()->after('privacy');
        });

        // Backfill: existing private communities need a token so they stay joinable.
        $privateIds = DB::table('communities')
            ->where('privacy', 'private')
            ->whereNull('join_token')
            ->pluck('id');

        foreach ($privateIds as $id) {
            DB::table('communities')->where('id', $id)->update([
                'join_token' => Str::upper(Str::random(8)),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropColumn('join_token');
        });
    }
};
