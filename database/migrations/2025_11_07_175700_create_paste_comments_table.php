<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paste_comments', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->foreignUuid('paste_id')->constrained('pastes');
            $table->foreignUuid('user_id')->constrained('users');
            $table->mediumText('content');
            $table->foreignUuid('syntax_highlight_id')->constrained('syntax_highlights');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paste_comments');
    }
};
