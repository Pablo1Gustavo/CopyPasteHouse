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
            $table->string('label', 35)->unique();
            $table->string('value', 25)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syntax_highlights');
    }
};
