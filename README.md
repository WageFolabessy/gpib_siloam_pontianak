<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Sistem Informasi Gereja

Aplikasi web berbasis Laravel untuk jemaat GPIB Jemaat “Siloam” Pontianak. Situs ini menyajikan renungan rohani, jadwal ibadah, arsip warta jemaat, tata ibadah, informasi gereja, serta fitur interaktif seperti live chat dan aksesibilitas text-to-speech (TTS) dan navigasi suara.

## Ringkasan Fitur

- Konten Publik
  - Beranda: menampilkan highlight Renungan terbaru.
  - Renungan:
    - Daftar renungan dengan tombol “Muat Lebih Banyak” (AJAX).
    - Halaman detail dengan navigasi “Sebelumnya/Berikutnya”, gambar thumbnail, serta kontrol TTS (play/pause/stop).
  - Jadwal Ibadah: daftar jadwal sesuai kategori.
  - Warta Jemaat: arsip warta yang dapat dilihat/diunduh (PDF).
  - Tata Ibadah: daftar tata ibadah (PDF) untuk diunduh.
  - Info Gereja: sejarah, visi-misi, serta daftar pendeta yang melayani.

- Aksesibilitas & Interaksi
  - Text-to-Speech: membaca konten penting (judul, isi renungan, dll) dengan highlight dinamis.
  - Navigasi Suara (Speech Recognition): perintah suara sederhana untuk membuka halaman seperti “beranda”, “jadwal ibadah”, “renungan”, “warta jemaat”, “tata ibadah”, dan “info”.
  - Live Chat:
    - Tamu (belum login): dapat mengirim pesan dari template, riwayat tidak disimpan.
    - Jemaat (login): riwayat percakapan tersimpan, dapat mengirim/terima pesan, dan admin dapat menandai pesan terbaca.

- Autentikasi Jemaat
  - Registrasi, login, logout.
  - Lupa/Reset password (menggunakan broker Laravel).
  - Profil pengguna: ubah nama, email, dan password.

- Dashboard Admin
  - Renungan:
    - CRUD dengan editor WYSIWYG (Summernote), upload thumbnail, validasi sisi server/klien.
    - Tabel DataTables (server-side) untuk pencarian, paging, urut.
  - Jadwal Ibadah: CRUD dengan DataTables server-side.
  - Warta Jemaat:
    - CRUD, unggah PDF (dengan validasi ukuran/tipe), status publish (Draft/Published).
  - Tata Ibadah: CRUD + unggah PDF.
  - Template Tanya Jawab: CRUD untuk bank template chat.
  - Data Pendeta: CRUD data pengurus/pelayan.
  - Admin Users: kelola akun admin.
  - Jemaat: kelola data jemaat (hapus, dsb).
  - Seluruh aksi CRUD memanfaatkan AJAX, feedback notifikasi, dan inisialisasi tooltip dinamis.

## Teknologi yang Digunakan

- Backend
  - Laravel (konfigurasi modern bootstrap/app.php)
  - Eloquent ORM
  - Guard terpisah: web (jemaat) dan admin_users (admin)
  - Yajra DataTables (server-side processing)
  - Validasi via Form Request, Reset Password Broker Laravel
  - Penyimpanan berkas melalui Storage (disk public)
  - Broadcasting/Realtime Chat: Laravel Broadcasting (driver Pusher) untuk event MessageSent, MessagesMarkedAsRead

- Frontend
  - Blade Template
  - Bootstrap 5.3, Font Awesome
  - jQuery 3.7
  - DataTables
  - Summernote (WYSIWYG)
  - SweetAlert2 (konfirmasi/alert di beberapa modul)
  - Web Speech API (SpeechSynthesis & SpeechRecognition) untuk TTS & navigasi suara
  - Vite untuk bundling aset
  - Realtime Chat: Laravel Echo + Pusher (Laravel Pusher) untuk pesan live, notifikasi, dan
