<?php

namespace Database\Seeders;

use App\Models\DaftarPelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DaftarPelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id_user'             => '',
                'id_cabang'           => '',
                'nama_lengkap'        => '0',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_user'             => '',
                'id_cabang'           => '',
                'nama_lengkap'        => '0',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_user'             => '',
                'id_cabang'           => '',
                'nama_lengkap'        => '0',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_user'             => '',
                'id_cabang'           => '',
                'nama_lengkap'        => '0',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],
        ];

        DaftarPelanggan::insert($data);
    }
}
