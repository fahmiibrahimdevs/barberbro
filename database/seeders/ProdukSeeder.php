<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id_cabang'           => '',
                'id_user'             => '0',
                'id_kategori'         => '',
                'id_satuan'           => '',
                'kode_item'           => '',
                'nama_item'           => '',
                'harga_jasa'          => '0',
                'harga_pokok'         => '0',
                'harga_jual'          => '0',
                'stock'               => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_cabang'           => '',
                'id_user'             => '0',
                'id_kategori'         => '',
                'id_satuan'           => '',
                'kode_item'           => '',
                'nama_item'           => '',
                'harga_jasa'          => '0',
                'harga_pokok'         => '0',
                'harga_jual'          => '0',
                'stock'               => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_cabang'           => '',
                'id_user'             => '0',
                'id_kategori'         => '',
                'id_satuan'           => '',
                'kode_item'           => '',
                'nama_item'           => '',
                'harga_jasa'          => '0',
                'harga_pokok'         => '0',
                'harga_jual'          => '0',
                'stock'               => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_cabang'           => '',
                'id_user'             => '0',
                'id_kategori'         => '',
                'id_satuan'           => '',
                'kode_item'           => '',
                'nama_item'           => '',
                'harga_jasa'          => '0',
                'harga_pokok'         => '0',
                'harga_jual'          => '0',
                'stock'               => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],
        ];

        Produk::insert($data);
    }
}
