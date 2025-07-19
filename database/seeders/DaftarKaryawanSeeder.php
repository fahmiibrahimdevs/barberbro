<?php

namespace Database\Seeders;

use App\Models\DaftarKaryawan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DaftarKaryawanSeeder extends Seeder
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
                'name'                => '',
                'email'               => '',
                'password'            => '',
                'tgl_lahir'           => date('Y-m-d'),
                'jk'                  => '',
                'alamat'              => '',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_user'             => '',
                'id_cabang'           => '',
                'name'                => '',
                'email'               => '',
                'password'            => '',
                'tgl_lahir'           => date('Y-m-d'),
                'jk'                  => '',
                'alamat'              => '',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_user'             => '',
                'id_cabang'           => '',
                'name'                => '',
                'email'               => '',
                'password'            => '',
                'tgl_lahir'           => date('Y-m-d'),
                'jk'                  => '',
                'alamat'              => '',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],

            [
                'id_user'             => '',
                'id_cabang'           => '',
                'name'                => '',
                'email'               => '',
                'password'            => '',
                'tgl_lahir'           => date('Y-m-d'),
                'jk'                  => '',
                'alamat'              => '',
                'no_telp'             => '0',
                'deskripsi'           => '',
                'gambar'              => '',
            ],
        ];

        DaftarKaryawan::insert($data);
    }
}
