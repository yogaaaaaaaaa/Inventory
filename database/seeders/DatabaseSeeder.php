<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
public function run()
{
    $data = [
        'Jatiuwung' => ['Alam Jaya', 'Gandasari'],
        'Priuk' => ['Gebang Raya', 'Gembor'],
        'Cibodas' => ['Cibodas', 'Cibodasari'],
    ];

    foreach ($data as $kecamatan => $kelurahans) {
        $kec = Kecamatan::create(['name' => $kecamatan]);
        foreach ($kelurahans as $kelurahan) {
            Kelurahan::create([
                'name' => $kelurahan,
                'kecamatan_id' => $kec->id
            ]);
        }
    }
}
}
