<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            Schema::create('pastes_temp', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignUuid('syntax_highlight_id')->constrained('syntax_highlights');
                $table->string('title', 50);
                $table->string('tags')->nullable();
                $table->mediumText('content');
                $table->boolean('listable')->default(true);
                $table->string('password', 60)->nullable();
                $table->timestamp('expiration')->nullable()->index();
                $table->boolean('destroy_on_open')->default(false);
                $table->timestamp('created_at');
            });

            DB::statement('INSERT INTO pastes_temp (id, user_id, syntax_highlight_id, title, tags, content, listable, password, expiration, destroy_on_open, created_at)
                SELECT id, user_id, syntax_highlight_id, title, tags, content, listable, password, expiration, destroy_on_open, created_at FROM pastes');

            Schema::drop('pastes');
            Schema::rename('pastes_temp', 'pastes');

            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            Schema::table('pastes', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('pastes', function (Blueprint $table) {
                $table->foreignUuid('user_id')->nullable()->change();
            });

            Schema::table('pastes', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            Schema::create('pastes_temp', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users');
                $table->foreignUuid('syntax_highlight_id')->constrained('syntax_highlights');
                $table->string('title', 50);
                $table->string('tags')->nullable();
                $table->mediumText('content');
                $table->boolean('listable')->default(true);
                $table->string('password', 60)->nullable();
                $table->timestamp('expiration')->nullable()->index();
                $table->boolean('destroy_on_open')->default(false);
                $table->timestamp('created_at');
            });

            DB::statement('INSERT INTO pastes_temp (id, user_id, syntax_highlight_id, title, tags, content, listable, password, expiration, destroy_on_open, created_at)
                SELECT id, user_id, syntax_highlight_id, title, tags, content, listable, password, expiration, destroy_on_open, created_at FROM pastes');

            Schema::drop('pastes');
            Schema::rename('pastes_temp', 'pastes');

            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            Schema::table('pastes', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('pastes', function (Blueprint $table) {
                $table->foreignUuid('user_id')->nullable(false)->change();
            });

            Schema::table('pastes', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
