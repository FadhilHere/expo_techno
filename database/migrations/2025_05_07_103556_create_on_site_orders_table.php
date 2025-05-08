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
        Schema::create('on_site_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('nama_pembeli');
            $table->decimal('total_amount', 12, 2);
            $table->timestamp('tanggal_pembelian');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('on_site_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('on_site_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('products')->onDelete('cascade');
            $table->string('nama_produk');
            $table->decimal('harga_satuan', 12, 2);
            $table->integer('qty');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('on_site_order_items');
        Schema::dropIfExists('on_site_orders');
    }
};
