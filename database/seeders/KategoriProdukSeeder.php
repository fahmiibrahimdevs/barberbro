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
                // 'id_cabang'           => '1',
                'nama_kategori'       => 'Produk Barbershop',
                'deskripsi'           => '',
            ],
            [
                // 'id_cabang'           => '1',
                'nama_kategori'       => 'Jasa Barbershop',
                'deskripsi'           => '',
            ],
            [
                // 'id_cabang'           => '1',
                'nama_kategori'       => 'Treatment',
                'deskripsi'           => '',
            ],
            [
                // 'id_cabang'           => '1',
                'nama_kategori'       => 'Produk Umum',
                'deskripsi'           => '',
            ],
        ];

        KategoriProduk::insert($data);
    }
}
