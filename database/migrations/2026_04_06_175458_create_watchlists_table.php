<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // dramabox, reelshort, etc.
            $table->string('drama_id');
            $table->string('drama_title');
            $table->text('drama_thumbnail')->nullable();
            $table->integer('total_episodes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'provider', 'drama_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
