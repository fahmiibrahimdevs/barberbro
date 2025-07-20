<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\CabangLokasi;
use App\Models\DaftarKaryawan;
use App\Models\KategoriProduk;
use App\Models\KategoriSatuan;
use Illuminate\Support\Facades\DB;

class GlobalDataService
{
    // Fungsi untuk mengambil data cabang
    public function getCabangs()
    {
        return DB::table('cabang_lokasi')->select('id', 'nama_cabang')->get();
    }

    // Fungsi untuk mengambil data kategori produk
    public function getKategoris()
    {
        return DB::table('kategori_produk')->select('id', 'nama_kategori')->get();
    }

    // Fungsi untuk mengambil data kategori satuan
    public function getSatuans()
    {
        return DB::table('kategori_satuan')->select('id', 'nama_satuan')->get();
    }

    public function getProduks()
    {
        return DB::table('produk')->select('id', 'nama_item')->get();
    }

    public function getPelanggans()
    {
        return DB::table('daftar_pelanggan')->select('id', 'nama_pelanggan')->get();
    }

    public function getKaryawans()
    {
        return DB::table('daftar_karyawan')->select('daftar_karyawan.id', 'name')
            ->join('users', 'users.id', 'daftar_karyawan.id_user')
            ->where('role_id', 'capster')
            ->get();
    }

    public function getProdukAndKategori()
    {
        return DB::table('produk')->select('produk.id', 'nama_item', 'harga_jasa', 'nama_kategori', 'produk.deskripsi')
            ->join('kategori_produk', 'kategori_produk.id', 'produk.id_kategori')
            ->get();
    }
}
