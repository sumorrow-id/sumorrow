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
            $table->text('review')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'mountain_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // Enforce score bounds in SQLite where ALTER TABLE ADD CONSTRAINT is unsupported.
            DB::statement("CREATE TRIGGER mountain_ratings_score_check_insert BEFORE INSERT ON mountain_ratings FOR EACH ROW WHEN NEW.score < 1 OR NEW.score > 5 BEGIN SELECT RAISE(ABORT, 'score must be between 1 and 5'); END;");
            DB::statement("CREATE TRIGGER mountain_ratings_score_check_update BEFORE UPDATE ON mountain_ratings FOR EACH ROW WHEN NEW.score < 1 OR NEW.score > 5 BEGIN SELECT RAISE(ABORT, 'score must be between 1 and 5'); END;");

            DB::statement("CREATE TRIGGER mountain_ratings_avg_after_insert AFTER INSERT ON mountain_ratings FOR EACH ROW BEGIN UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = NEW.mountain_id), 0) WHERE id = NEW.mountain_id; END;");
            DB::statement("CREATE TRIGGER mountain_ratings_avg_after_update AFTER UPDATE ON mountain_ratings FOR EACH ROW BEGIN UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = NEW.mountain_id), 0) WHERE id = NEW.mountain_id; UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = OLD.mountain_id), 0) WHERE id = OLD.mountain_id; END;");
            DB::statement("CREATE TRIGGER mountain_ratings_avg_after_delete AFTER DELETE ON mountain_ratings FOR EACH ROW BEGIN UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = OLD.mountain_id), 0) WHERE id = OLD.mountain_id; END;");
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE mountain_ratings ADD CONSTRAINT mountain_ratings_score_check CHECK (score BETWEEN 1 AND 5)');

            DB::unprepared('CREATE TRIGGER mountain_ratings_avg_after_insert AFTER INSERT ON mountain_ratings FOR EACH ROW UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = NEW.mountain_id), 0) WHERE id = NEW.mountain_id');
            DB::unprepared('CREATE TRIGGER mountain_ratings_avg_after_update AFTER UPDATE ON mountain_ratings FOR EACH ROW BEGIN UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = NEW.mountain_id), 0) WHERE id = NEW.mountain_id; UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = OLD.mountain_id), 0) WHERE id = OLD.mountain_id; END');
            DB::unprepared('CREATE TRIGGER mountain_ratings_avg_after_delete AFTER DELETE ON mountain_ratings FOR EACH ROW UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = OLD.mountain_id), 0) WHERE id = OLD.mountain_id');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE mountain_ratings ADD CONSTRAINT mountain_ratings_score_check CHECK (score BETWEEN 1 AND 5)');

            DB::unprepared("CREATE OR REPLACE FUNCTION refresh_mountain_avg_rating(mountain_uuid uuid) RETURNS void AS $$ BEGIN UPDATE mountains SET avg_rating = COALESCE((SELECT AVG(score) FROM mountain_ratings WHERE mountain_id = mountain_uuid), 0) WHERE id = mountain_uuid; END; $$ LANGUAGE plpgsql");
            DB::unprepared("CREATE OR REPLACE FUNCTION mountain_ratings_avg_after_write() RETURNS TRIGGER AS $$ BEGIN IF TG_OP = 'DELETE' THEN PERFORM refresh_mountain_avg_rating(OLD.mountain_id); RETURN OLD; END IF; PERFORM refresh_mountain_avg_rating(NEW.mountain_id); IF TG_OP = 'UPDATE' AND NEW.mountain_id <> OLD.mountain_id THEN PERFORM refresh_mountain_avg_rating(OLD.mountain_id); END IF; RETURN NEW; END; $$ LANGUAGE plpgsql");
            DB::unprepared('CREATE TRIGGER mountain_ratings_avg_after_insert AFTER INSERT ON mountain_ratings FOR EACH ROW EXECUTE FUNCTION mountain_ratings_avg_after_write()');
            DB::unprepared('CREATE TRIGGER mountain_ratings_avg_after_update AFTER UPDATE ON mountain_ratings FOR EACH ROW EXECUTE FUNCTION mountain_ratings_avg_after_write()');
            DB::unprepared('CREATE TRIGGER mountain_ratings_avg_after_delete AFTER DELETE ON mountain_ratings FOR EACH ROW EXECUTE FUNCTION mountain_ratings_avg_after_write()');
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
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_insert');
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_update');
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_delete');
        } elseif ($driver === 'mysql') {
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_insert');
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_update');
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_delete');
        } elseif ($driver === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_insert ON mountain_ratings');
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_update ON mountain_ratings');
            DB::statement('DROP TRIGGER IF EXISTS mountain_ratings_avg_after_delete ON mountain_ratings');
            DB::statement('DROP FUNCTION IF EXISTS mountain_ratings_avg_after_write');
            DB::statement('DROP FUNCTION IF EXISTS refresh_mountain_avg_rating(uuid)');
        }

        Schema::dropIfExists('mountain_ratings');
    }
};

