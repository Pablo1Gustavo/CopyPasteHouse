<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expiration_times', function (Blueprint $table)
        {
            $table->unsignedMediumInteger('minutes')->primary();
            $table->string('label', 15)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expiration_times');
    }
};
