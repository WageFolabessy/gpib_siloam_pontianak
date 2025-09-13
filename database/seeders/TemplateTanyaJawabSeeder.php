<?php

namespace Database\Seeders;

use App\Models\TemplateTanyaJawab;
use Illuminate\Database\Seeder;

class TemplateTanyaJawabSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'pertanyaan' => 'Bagaimana cara melihat jadwal ibadah?',
                'jawaban' => 'Buka menu "Jadwal" di navigasi atau kunjungi halaman Jadwal Ibadah.',
            ],
            [
                'pertanyaan' => 'Di mana saya dapat membaca renungan terbaru?',
                'jawaban' => 'Renungan terbaru tersedia di halaman Beranda dan daftar lengkapnya di halaman Renungan.',
            ],
            [
                'pertanyaan' => 'Bagaimana mengunduh Warta Jemaat?',
                'jawaban' => 'Masuk ke halaman Warta Jemaat, lalu klik tombol "Lihat PDF" atau "Unduh".',
            ],
            [
                'pertanyaan' => 'Apakah tersedia fitur Text-to-Speech?',
                'jawaban' => 'Ya, Anda dapat menggunakan kontrol TTS pada halaman Renungan dan halaman lainnya yang mendukung.',
            ],
        ];

        foreach ($items as $row) {
            TemplateTanyaJawab::create($row);
        }
    }
}