<?php

namespace Database\Seeders;

use App\Models\JadwalIbadah;
use Illuminate\Database\Seeder;

class JadwalIbadahSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Ibadah Minggu
            ['keterangan' => 'Ibadah Minggu Pagi', 'hari' => 'Minggu', 'jam' => '07:00', 'kategori' => 'Ibadah Minggu'],
            ['keterangan' => 'Ibadah Minggu Siang', 'hari' => 'Minggu', 'jam' => '10:00', 'kategori' => 'Ibadah Minggu'],
            ['keterangan' => 'Ibadah Minggu Sore', 'hari' => 'Minggu', 'jam' => '17:00', 'kategori' => 'Ibadah Minggu'],

            // Ibadah Pelkat
            ['keterangan' => 'Ibadah PKB', 'hari' => 'Rabu', 'jam' => '19:00', 'kategori' => 'Ibadah Pelkat'],
            ['keterangan' => 'Ibadah PW', 'hari' => 'Kamis', 'jam' => '19:00', 'kategori' => 'Ibadah Pelkat'],
            ['keterangan' => 'Ibadah GP', 'hari' => 'Jumat', 'jam' => '19:00', 'kategori' => 'Ibadah Pelkat'],
        ];

        foreach ($items as $row) {
            JadwalIbadah::create($row);
        }
    }
}
