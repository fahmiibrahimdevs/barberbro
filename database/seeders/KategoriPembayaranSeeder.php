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
                    'nama_kategori'       => 'Tunai',
                    'deskripsi'           => '',
                ],
                [
                    'nama_kategori'       => 'GoPay',
                    'deskripsi'           => '',
                ],
                [
                    'nama_kategori'       => 'Dana',
                    'deskripsi'           => '',
                ],
                [
                    'nama_kategori'       => 'ShopeePay',
                    'deskripsi'           => '',
                ],
                [
                    'nama_kategori'       => 'QRIS',
                    'deskripsi'           => '',
                ],
                [
                    'nama_kategori'       => 'LinkAja',
                    'deskripsi'           => '',
                ],
                [
                    'nama_kategori'       => 'OVO',
                    'deskripsi'           => '',
                ],
                [
                    'nama_kategori'       => 'Transfer',
                    'deskripsi'           => '',
                ],
            ];

            KategoriPembayaran::insert($data);
        }
    }
}
