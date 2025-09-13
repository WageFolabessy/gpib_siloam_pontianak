<?php

namespace Database\Seeders;

use App\Models\Pendeta;
use Illuminate\Database\Seeder;

class PendetaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'Pdt. Andreas Simanjuntak', 'kategori' => 'Pendeta Jemaat'],
            ['nama' => 'Pdt. Maria Sitorus', 'kategori' => 'Pendeta Pembantu'],
            ['nama' => 'Penatua Budi Santoso', 'kategori' => 'Majelis'],
        ];

        foreach ($items as $row) {
            Pendeta::create($row);
        }
    }
}