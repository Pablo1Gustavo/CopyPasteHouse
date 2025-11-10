<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syntax_highlights', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->string('name', 35)->unique();
            $table->string('extension', 25)->unique();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syntax_highlights');
    }
};
