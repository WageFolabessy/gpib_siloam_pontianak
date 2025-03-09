<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="garisAs" content="projek">
    <title>GPIB SILOAM PONTIANAK @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('assets/pages/img/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/pages/css/bootstrap.min.css') }}">
    <link href="{{ asset('assets/dashboard/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/pages/css/style.css') }}">
    @yield('style')
    <style>
        /* Tampilan ikon chat untuk desktop */
        #chatButton {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            z-index: 1050;
        }
        /* Tampilan ikon chat untuk mobile */
        @media (max-width: 576px) {
            #chatButton {
                bottom: 10px;
                right: 10px;
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <!-- Informasi Fitur Speech Synthesis -->
    <div class="alert alert-info text-center mb-0">
        Website ini dilengkapi dengan fitur <strong>speech synthesis</strong> dan <strong>speech recognition</strong>.
        <br>
        Anda dapat berpindah halaman dengan perintah suara, contohnya: <em>"Buka halaman jadwal ibadah"</em>, <em>"Buka
            halaman renungan"</em>, atau <em>"Buka halaman info"</em>.
        <br>
        <span id="browserSupport"></span>
    </div>
    @include('components/navbar')
    @yield('content')
    @include('components/footer')
    <!-- Icon Chat (fixed di kanan bawah) -->
    <button type="button" class="btn btn-primary rounded-circle" id="chatButton" data-bs-toggle="modal"
        data-bs-target="#chatModal">
        <i class="fas fa-comments" style="font-size: 24px;"></i>
    </button>

    <!-- Chat Modal -->
    <div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="chatModalLabel">Chat dengan Admin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 300px; overflow-y: auto;">
                    <!-- Area chat: integrasikan websocket atau sistem chat lainnya di sini -->
                    <div id="chatMessages">
                        <div class="mb-2">
                            <strong>Admin:</strong> Halo, ada yang bisa saya bantu?
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="text" id="chatInput" class="form-control" placeholder="Ketik pesan Anda...">
                    <button type="button" class="btn btn-primary" id="sendChat">Kirim</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/pages/js/bootstrap.bundle.min.js') }}"></script>
    @yield('script')

    <script>
        function checkBrowserSupport() {
            var supportMessage = "";
            // Cek speechSynthesis dan SpeechRecognition (atau webkitSpeechRecognition)
            if (window.speechSynthesis && (window.SpeechRecognition || window.webkitSpeechRecognition)) {
                supportMessage = "Browser Anda mendukung fitur Speech Synthesis dan Speech Recognition.";
            } else {
                supportMessage =
                    "Browser Anda TIDAK mendukung fitur Speech Synthesis dan/atau Speech Recognition. Beberapa fitur mungkin tidak berfungsi.";
            }
            document.getElementById('browserSupport').innerText = supportMessage;
        }

        document.addEventListener("DOMContentLoaded", function() {
            checkBrowserSupport();
        });

        // Deklarasikan variabel global speechEnabled (default aktif)
        window.speechEnabled = false;

        // Fungsi untuk mengubah tampilan tombol sesuai status
        function updateToggleButton() {
            const btn = document.getElementById("btnToggleSpeech");
            if (window.speechEnabled) {
                btn.classList.remove("btn-success");
                btn.classList.add("btn-danger");
                btn.innerText = "Nonaktifkan Speech";
            } else {
                btn.classList.remove("btn-danger");
                btn.classList.add("btn-success");
                btn.innerText = "Aktifkan Speech";
            }
        }

        // Inisialisasi tampilan tombol saat halaman dimuat
        updateToggleButton();

        // Event listener untuk tombol toggle
        document.getElementById("btnToggleSpeech").addEventListener("click", function() {
            window.speechEnabled = !window.speechEnabled;
            if (!window.speechEnabled) {
                window.speechSynthesis.cancel();
                alert("Speech synthesis dinonaktifkan.");
            } else {
                alert("Speech synthesis diaktifkan.");
            }
            updateToggleButton();
        });

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (SpeechRecognition) {
            const recognition = new SpeechRecognition();
            // Set pengenalan suara secara kontinu (opsional) dan bahasa Indonesia
            recognition.continuous = true;
            recognition.lang = "id-ID";

            // Mulai mendengarkan
            recognition.start();

            recognition.onresult = function(event) {
                // Ambil hasil pengenalan suara
                const transcript = event.results[event.results.length - 1][0].transcript.trim().toLowerCase();
                console.log("Recognized: ", transcript);

                // Contoh perintah: "buka halaman jadwal ibadah"
                if (transcript.includes("buka halaman jadwal ibadah")) {
                    window.location.href = "/jadwal-ibadah";
                }
                // Anda dapat menambahkan perintah lain, misalnya:
                else if (transcript.includes("buka halaman beranda")) {
                    window.location.href = "/";
                } else if (transcript.includes("buka halaman renungan")) {
                    window.location.href = "/renungan";
                } else if (transcript.includes("buka halaman info")) {
                    window.location.href = "/info";
                }
            };

            // recognition.onerror = function(event) {
            //     console.error("Speech recognition error:", event.error);
            // };

            // Jika perlu, restart recognizer setelah selesai
            recognition.onend = function() {
                recognition.start();
            };
        } else {
            console.log("Browser tidak mendukung Speech Recognition API.");
        }
    </script>

</body>

</html>
