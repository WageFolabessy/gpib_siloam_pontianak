<?php

namespace Database\Seeders;

use App\Models\Renungan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RenunganSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'judul' => 'Kasih Karunia yang Mengubahkan',
                'alkitab' => 'Efesus 2:8-9',
                'bacaan_alkitab' => 'Kasih Karunia',
                'isi_bacaan' => '<p>Kasih karunia Allah memampukan kita menjadi ciptaan baru, bukan karena usaha kita, tetapi karena anugerah-Nya.</p>',
            ],
            [
                'judul' => 'Iman yang Bekerja oleh Kasih',
                'alkitab' => 'Galatia 5:6',
                'bacaan_alkitab' => 'Iman dan Kasih',
                'isi_bacaan' => '<p>Iman yang sejati dinyatakan melalui kasih dan ketaatan pada Firman Tuhan.</p>',
            ],
            [
                'judul' => 'Pengharapan yang Tidak Memalukan',
                'alkitab' => 'Roma 5:5',
                'bacaan_alkitab' => 'Pengharapan',
                'isi_bacaan' => '<p>Roh Kudus mencurahkan kasih Allah dalam hati kita sehingga pengharapan kita tidak memalukan.</p>',
            ],
            [
                'judul' => 'Hidup dalam Terang',
                'alkitab' => '1 Yohanes 1:7',
                'bacaan_alkitab' => 'Terang',
                'isi_bacaan' => '<p>Berjalan dalam terang membawa persekutuan yang sejati dan pemurnian dari segala dosa.</p>',
            ],
            [
                'judul' => 'Tenanglah, Aku Ini',
                'alkitab' => 'Markus 6:50',
                'bacaan_alkitab' => 'Penghiburan',
                'isi_bacaan' => '<p>Di tengah badai hidup, Yesus hadir dan menenangkan hati kita: "Tenanglah, Aku ini."</p>',
            ],
            [
                'judul' => 'Bersyukur dalam Segala Hal',
                'alkitab' => '1 Tesalonika 5:18',
                'bacaan_alkitab' => 'Ucapan Syukur',
                'isi_bacaan' => '<p>Ucapan syukur adalah kehendak Allah; membuka mata kita pada kebaikan-Nya setiap hari.</p>',
            ],
        ];

        Storage::disk('public')->makeDirectory('thumbnails');

        foreach ($items as $it) {
            $judul = $it['judul'];
            $slug = Str::slug($judul);
            $fileName = $slug . '.png';
            $path = 'thumbnails/' . $fileName;

            // Force PNG placeholder and ensure valid image response
            $imageUrl = 'https://placehold.co/800x450/png?text=' . urlencode($judul);
            $thumbnailPath = null;

            try {
                $response = Http::accept('image/png')
                    ->withHeaders(['User-Agent' => 'GPIBSeeder/1.0'])
                    ->timeout(15)
                    ->get($imageUrl);

                if ($response->ok()) {
                    $mime = $response->header('Content-Type');
                    if ($mime && Str::startsWith($mime, 'image/')) {
                        Storage::disk('public')->put($path, $response->body());
                        $thumbnailPath = $path;
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan jika gagal download; thumbnail akan null
            }

            Renungan::create([
                'judul' => $judul,
                'slug' => $slug,
                'alkitab' => $it['alkitab'],
                'bacaan_alkitab' => $it['bacaan_alkitab'],
                'thumbnail' => $thumbnailPath, // relative to public disk
                'isi_bacaan' => $it['isi_bacaan'],
            ]);
        }
    }
}