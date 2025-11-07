<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table)
        {
            $table->foreignUuid('user_id')->primary()->constrained('users');
            $table->string('timezone', 40)->default('UTC');
            $table->string('language', 10)->default('en');
            $table->enum('theme', ['light', 'dark', 'system'])->default('system');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
