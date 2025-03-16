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
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
        }

        @media (max-width:576px) {
            #chatButton {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <div class="alert alert-info text-center mb-0">
        Website ini dilengkapi dengan fitur <strong>speech synthesis</strong> dan <strong>speech recognition</strong>.
        <br>
        Anda dapat berpindah halaman dengan perintah suara, contohnya:
        <em>"Buka halaman beranda"</em>, "Buka halaman jadwal ibadah"</em>, <em>"Buka halaman renungan"</em>, atau
        <em>"Buka halaman info"</em>.
        <br>
        <span id="browserSupport"></span>
    </div>
    @include('components/navbar')
    @yield('content')
    @include('components/footer')

    <!-- Icon Chat -->
    <button type="button" class="btn btn-primary rounded-circle" id="chatButton" data-bs-toggle="modal"
        data-bs-target="#chatModal">
        <i class="fas fa-comments" style="font-size:24px"></i>
    </button>

    @include('chat/modal-chat')

    {{-- JavaScript Untuk Jquery v3.7.0 dan Bootstrap v5.3.0 --}}
    <script src="{{ asset('assets/dashboard/vendor/jquery/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/pages/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/pages/js/speechsynthesis/index.js') }}"></script>
    @vite('resources/js/app.js')
    <script src="{{ asset('assets/pages/js/chat/chat-user.js') }}"></script>

    <script type="module">
        // Mendengarkan event broadcast untuk pesan user
        Echo.channel("chat-room").listen("UserMessageSent", (e) => {
            if (e.message.user_id && e.message.user_id === userId) {
                // Jika pesan yang diterima sama dengan pesan template yang baru saja ditampilkan,
                // abaikan agar tidak tampil ganda, lalu reset flag-nya
                if (lastTemplateQuestion && e.message.message === lastTemplateQuestion) {
                    lastTemplateQuestion = "";
                    return;
                }
                console.log("Pesan user diterima:", e.message);
                appendMessageToChat(e.message.message, "user", e.message.timestamp, true, {
                    user_id: e.message.user_id,
                });
            }
        });

        // Mendengarkan event broadcast untuk pesan admin
        Echo.channel("chat-room").listen("AdminMessageSent", (e) => {
            // Tampilkan pesan admin jika target sesuai dengan userId
            if (e.message.target && e.message.target === userId) {
                console.log("Pesan admin diterima:", e.message);
                appendMessageToChat(e.message.message, "admin", e.message.timestamp, true, {
                    target: e.message.target
                });
            }
        });
    </script>
    @yield('script')

</body>

</html>
