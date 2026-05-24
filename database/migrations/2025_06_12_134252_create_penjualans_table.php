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
        Schema::create('penjualans', function (Blueprint $table) {
    $table->id();
    $table->string('no_transaksi');
    $table->foreignId('pengguna_id')->constrained('penggunas')->onDelete('cascade');
    $table->string('nama_pelanggan');
    $table->date('tanggal_penjualan');
    $table->string('metode_pembayaran');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
