<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expo_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_expo_id')->constrained('tahun_expo')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('cover_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('expo_history_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expo_history_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expo_history_images');
        Schema::dropIfExists('expo_histories');
    }
};
