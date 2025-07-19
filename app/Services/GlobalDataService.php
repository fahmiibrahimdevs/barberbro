<?php

namespace App\Services;

use App\Models\CabangLokasi;
use App\Models\KategoriProduk;
use App\Models\KategoriSatuan;
use App\Models\Produk;

class GlobalDataService
{
    // Fungsi untuk mengambil data cabang
    public function getCabangs()
    {
        return CabangLokasi::select('id', 'nama_cabang')->get();
    }

    // Fungsi untuk mengambil data kategori produk
    public function getKategoris()
    {
        return KategoriProduk::select('id', 'nama_kategori')->get();
    }

    // Fungsi untuk mengambil data kategori satuan
    public function getSatuans()
    {
        return KategoriSatuan::select('id', 'nama_satuan')->get();
    }

    public function getProduks()
    {
        return Produk::select('id', 'nama_item')->get();
    }
}
