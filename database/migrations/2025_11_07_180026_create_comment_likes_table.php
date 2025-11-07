<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_likes', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->foreignUuid('comment_id')->constrained('paste_comments');
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamp('liked_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_likes');
    }
};
