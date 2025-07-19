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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->text('id_cabang')->default('');
            $table->text('id_user')->default('');
            $table->text('id_kategori')->default('');
            $table->text('id_satuan')->default('');
            $table->text('kode_item')->default('');
            $table->text('nama_item')->default('');
            $table->text('harga_jasa')->default('');
            $table->text('harga_pokok')->default('');
            $table->text('harga_jual')->default('');
            $table->text('stock')->default('');
            $table->text('deskripsi')->default('');
            $table->text('gambar')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
