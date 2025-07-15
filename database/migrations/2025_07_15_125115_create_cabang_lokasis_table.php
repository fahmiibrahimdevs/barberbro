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
        Schema::create('cabang_lokasi', function (Blueprint $table) {
            $table->id();
            $table->text('nama_cabang');
            $table->text('alamat')->default('');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('no_telp')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabang_lokasi');
    }
};
