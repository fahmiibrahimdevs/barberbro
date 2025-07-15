<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_kategori'       => 'Produk',
                'deskripsi'           => '',
            ],
            [
                'nama_kategori'       => 'Jasa',
                'deskripsi'           => '',
            ],
        ];

        KategoriProduk::insert($data);
    }
}
