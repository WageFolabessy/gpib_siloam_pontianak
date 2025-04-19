<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="garisAs" content="projek">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GPIB SILOAM PONTIANAK @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('assets/pages/img/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/pages/css/bootstrap.min.css') }}">
    <link href="{{ asset('assets/dashboard/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/pages/css/style.css') }}">
    @yield('style')
    <style>
        #chatButton {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .2);
        }

        #speech-rec-toggle {
            position: fixed;
            bottom: 20px;
            right: 90px;
            width: 60px;
            height: 60px;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .2);
        }


        @media (max-width:576px) {
            #chatButton {
                bottom: 15px;
                right: 15px;
                width: 50px;
                height: 50px;
            }

            #chatButton i {
                font-size: 20px;
            }

            #speech-rec-toggle {
                bottom: 15px;
                right: 75px;
                width: 50px;
                height: 50px;
            }

            #speech-rec-toggle i {
                font-size: 20px;

            }
        }

        #chatCount {
            font-size: 0.7rem;
            line-height: 1;
            padding: 0.25em 0.5em;
        }

        .btn-brown {
            color: #fff !important;
            /* Paksa warna teks putih */
            background-color: #8E4818 !important;
            border-color: #8E4818 !important;
        }

        .btn-brown:hover,
        .btn-brown:focus,
        .btn-brown.focus {
            color: #fff !important;
            background-color: #7b3e14 !important;
            border-color: #703712 !important;
            box-shadow: 0 0 0 0.25rem rgba(142, 72, 24, 0.5) !important;
        }

        .btn-brown:active,
        .btn-brown.active {
            color: #fff !important;
            background-color: #703712 !important;
            border-color: #643110 !important;
        }

        .btn-brown:focus {
            box-shadow: 0 0 0 0.25rem rgba(142, 72, 24, 0.5) !important;
        }


        .btn-brown:disabled,
        .btn-brown.disabled {
            color: #fff !important;
            background-color: #8E4818 !important;
            border-color: #8E4818 !important;
        }

        :root {
            --custom-brown: #8E4818;
            --custom-brown-rgb: 142, 72, 24;
            --custom-brown-text-hover: #fff;
        }

        .btn-outline-brown {
            --bs-btn-color: var(--custom-brown);
            --bs-btn-border-color: var(--custom-brown);

            --bs-btn-hover-color: var(--custom-brown-text-hover);
            --bs-btn-hover-bg: var(--custom-brown);
            --bs-btn-hover-border-color: var(--custom-brown);

            --bs-btn-focus-shadow-rgb: var(--custom-brown-rgb);

            --bs-btn-active-color: var(--custom-brown-text-hover);
            --bs-btn-active-bg: var(--custom-brown);
            --bs-btn-active-border-color: var(--custom-brown);
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);

            --bs-btn-disabled-color: var(--custom-brown);
            --bs-btn-disabled-bg: transparent;
            --bs-btn-disabled-border-color: var(--custom-brown);
            --bs-gradient: none;
        }

        .text-brown {
            color: var(--custom-brown) !important;
        }

        a.text-brown:hover,
        a.text-brown:focus {
            color: var(--custom-brown-darker) !important;
        }
    </style>
</head>

<body class="bg-light">
    <div class="alert alert-info text-center mb-0 rounded-0 border-0 small py-2" role="alert">
        <i class="fas fa-info-circle me-1"></i>
        Website ini dilengkapi fitur <strong>Text-to-Speech</strong> & <strong>Navigasi Suara</strong>.
        Izinkan akses mikrofon jika diminta oleh browser untuk menggunakan navigasi suara.
        Contoh perintah: <em>"Buka beranda"</em>, <em>"Lihat jadwal ibadah"</em>, <em>"Baca renungan"</em>,
        <em>"Tampilkan info"</em>.
        <br>
        <span id="browserSupport" class="fw-bold"></span>
    </div>

    @include('components.navbar')

    <main class="py-4">
        @yield('content')
    </main>

    @include('components.footer')

    <button type="button" class="btn btn-brown rounded-circle" id="speech-rec-toggle" title="Aktifkan Perintah Suara"
        aria-label="Kontrol Perintah Suara">
        <i class="fas fa-microphone-slash"></i> {{-- Ikon awal --}}
    </button>
    <span id="speech-rec-status" class="visually-hidden">Perintah Suara Nonaktif</span>


    {{-- Tombol Chat Fixed --}}
    <button type="button" class="btn btn-primary rounded-circle" id="chatButton" data-bs-toggle="modal"
        data-bs-target="#chatModal" aria-label="Buka Chat">
        <i class="fas fa-comments" style="font-size:24px"></i>
        <span id="chatIconBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="display: none; align-items: center; justify-content: center;">
            0
        </span>
    </button>

    {{-- Include Modal Chat --}}
    @include('chat.modal-chat')

    <script src="{{ asset('assets/dashboard/vendor/jquery/jquery-3.7.0.min.js') }}"></script>

    <script src="{{ asset('assets/pages/js/bootstrap.bundle.min.js') }}"></script>

    @vite('resources/js/app.js')

    <script src="{{ asset('assets/pages/js/speechsynthesis/index.js') }}"></script>
    <script src="{{ asset('assets/pages/js/speechsynthesis/sta.js') }}"></script>
    <script src="{{ asset('assets/pages/js/chat/chat-user.js') }}" type="module" defer></script>)

    @yield('script')

</body>

</html>
