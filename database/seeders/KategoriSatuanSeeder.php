<?php

namespace Database\Seeders;

use App\Models\KategoriSatuan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_satuan'         => 'Pcs',
                'deskripsi'           => '',
            ],
            [
                'nama_satuan'         => 'Kali',
                'deskripsi'           => '',
            ],
            [
                'nama_satuan'         => 'Jam',
                'deskripsi'           => '',
            ],
        ];

        KategoriSatuan::insert($data);
    }
}
