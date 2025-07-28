<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriKeuangan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                // 'id_cabang'           => '1',
                'nama_kategori'       => 'Pemasukan',
                'kategori'            => 'Pemasukan',
            ],

            [
                // 'id_cabang'           => '1',
                'nama_kategori'       => 'Biaya Operasional',
                'kategori'            => 'Pengeluaran',
            ],
        ];

        KategoriKeuangan::insert($data);
    }
}
