<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');  // Relasi ke tabel orders
            $table->date('tanggal_pengembalian')->nullable();  // Tanggal pengembalian produk
            $table->enum('status', ['Perlu Dikembalikan', 'Terlambat', 'Dikembalikan'])->default('Perlu Dikembalikan');  // Status pengembalian
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};
