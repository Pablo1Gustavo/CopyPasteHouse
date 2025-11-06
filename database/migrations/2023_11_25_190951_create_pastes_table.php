<?php
use App\Models\SyntaxHighlight;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pastes', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->unsignedSmallInteger('syntax_highlight_id')->nullable();
            $table->foreign('syntax_highlight_id')->references('id')->on('syntax_highlights');
            
            $table->string('title', 50);
            $table->string('tags')->nullable();
            $table->mediumText('content');
            $table->boolean('listable')->default(true);
            $table->string('password', 60)->nullable();
            $table->timestamp('expiration')->nullable()->index();
            $table->boolean('destroy_on_open')->default(false);
            $table->timestamp('created_at');

            $table->fullText(['title', 'tags']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pastes');
    }
};
