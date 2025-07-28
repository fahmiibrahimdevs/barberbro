<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPembayaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { {
            $data = [
                [
                    // 'id_cabang'           => '1',
                    'nama_kategori'       => 'Tunai',
                    'deskripsi'           => '',
                ],
                [
                    // 'id_cabang'           => '1',
                    'nama_kategori'       => 'QRIS',
                    'deskripsi'           => '',
                ],
                [
                    // 'id_cabang'           => '1',
                    'nama_kategori'       => 'Transfer',
                    'deskripsi'           => '',
                ],
            ];

            KategoriPembayaran::insert($data);
        }
    }
}
