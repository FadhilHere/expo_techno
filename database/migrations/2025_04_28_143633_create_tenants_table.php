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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_expo_id')->constrained('tahun_expo')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori_tenants')->onDelete('cascade');
            $table->string('logo')->nullable();
            $table->string('nama_tenant');
            $table->text('deskripsi');
            $table->string('whatsapp_tenant')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
