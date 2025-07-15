<?php

namespace Database\Seeders;

use App\Models\CabangLokasi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CabangLokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_cabang'         => 'CheckPoint Barbershop',
                'alamat'              => 'Jl. Otto Iskandardinata, Karanganyar, Kec. Subang, Kabupaten Subang, Jawa Barat',
                'status'              => 'aktif',
                'no_telp'             => '085216003456',
            ],
        ];

        CabangLokasi::insert($data);
    }
}
