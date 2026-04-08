<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider');
            $table->string('drama_id');
            $table->string('drama_title');
            $table->text('drama_thumbnail')->nullable();
            $table->integer('episode_number')->default(1);
            $table->string('episode_title')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'provider', 'drama_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
