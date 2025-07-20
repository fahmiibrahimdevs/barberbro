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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->text('id_cabang')->nullable();
            $table->text('no_transaksi')->nullable();
            $table->text('tanggal')->nullable();
            $table->text('id_pelanggan')->nullable();
            $table->text('id_karyawan')->nullable();
            $table->text('total_komisi_karyawan')->nullable();
            $table->text('sub_total')->nullable();
            $table->text('total_akhir')->nullable();
            $table->text('id_metode_pembayaran')->nullable();
            $table->text('dibayar')->nullable();
            $table->text('kembalian')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
