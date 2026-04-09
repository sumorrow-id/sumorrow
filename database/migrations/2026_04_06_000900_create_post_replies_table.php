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
        Schema::create('post_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_reply_id')->nullable()->constrained('post_replies')->nullOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        //  MySQL DB Statement
        DB::statement('DROP TRIGGER IF EXISTS post_replies_depth_guard_insert');
        DB::statement('DROP TRIGGER IF EXISTS post_replies_depth_guard_update');

        DB::unprepared("CREATE TRIGGER post_replies_depth_guard_insert BEFORE INSERT ON post_replies FOR EACH ROW BEGIN IF NEW.parent_reply_id IS NOT NULL AND ((SELECT post_id FROM post_replies WHERE id = NEW.parent_reply_id LIMIT 1) <> NEW.post_id OR EXISTS (SELECT 1 FROM post_replies p WHERE p.id = NEW.parent_reply_id AND p.parent_reply_id IS NOT NULL)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'post replies can only be nested 2 levels and must stay in the same post'; END IF; END");
        DB::unprepared("CREATE TRIGGER post_replies_depth_guard_update BEFORE UPDATE ON post_replies FOR EACH ROW BEGIN IF NEW.parent_reply_id IS NOT NULL AND ((SELECT post_id FROM post_replies WHERE id = NEW.parent_reply_id LIMIT 1) <> NEW.post_id OR EXISTS (SELECT 1 FROM post_replies p WHERE p.id = NEW.parent_reply_id AND p.parent_reply_id IS NOT NULL)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'post replies can only be nested 2 levels and must stay in the same post'; END IF; END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('DROP TRIGGER IF EXISTS post_replies_depth_guard_insert');
            DB::statement('DROP TRIGGER IF EXISTS post_replies_depth_guard_update');
        }

        Schema::dropIfExists('post_replies');
    }
};

