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
        Schema::create('mountain_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('mountain_id')->constrained('mountains')->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'mountain_id']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            // Enforce score bounds in SQLite where ALTER TABLE ADD CONSTRAINT is unsupported.
            DB::statement("CREATE TRIGGER mountain_ratings_score_check_insert BEFORE INSERT ON mountain_ratings FOR EACH ROW WHEN NEW.score < 1 OR NEW.score > 5 BEGIN SELECT RAISE(ABORT, 'score must be between 1 and 5'); END;");
            DB::statement("CREATE TRIGGER mountain_ratings_score_check_update BEFORE UPDATE ON mountain_ratings FOR EACH ROW WHEN NEW.score < 1 OR NEW.score > 5 BEGIN SELECT RAISE(ABORT, 'score must be between 1 and 5'); END;");
        } else {
            DB::statement('ALTER TABLE mountain_ratings ADD CONSTRAINT mountain_ratings_score_check CHECK (score BETWEEN 1 AND 5)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_score_check_insert');
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_score_check_update');
        }

        Schema::dropIfExists('mountain_ratings');
    }
};


