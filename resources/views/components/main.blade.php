<!DOCTYPE html>
<html lang="en">

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
        Izinkan microphone untuk menggunakan fitur <strong>speech recognition</strong>.
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
        <span id="chatCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="display: none;">0</span>
    </button>

    @include('chat/modal-chat')

    {{-- JavaScript Untuk Jquery v3.7.0 dan Bootstrap v5.3.0 --}}
    <script src="{{ asset('assets/dashboard/vendor/jquery/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/pages/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/pages/js/speechsynthesis/index.js') }}"></script>
    @vite('resources/js/app.js')
    <script src="{{ asset('assets/pages/js/chat/chat-user.js') }}"></script>

    <script type="module">
        @if (Auth::check())
            window.userId = "{{ Auth::user()->id }}";
            window.userName = "Anda";
            window.currentRole = "user";
        @else
            let tempUserId = localStorage.getItem("userId");
            if (!tempUserId) {
                tempUserId = "user_" + Math.random().toString(36).substring(2, 10);
                localStorage.setItem("userId", tempUserId);
            }
            window.userId = tempUserId;
            window.userName = "";
            window.currentRole = "user";
        @endif

        window.sentMessageIds = new Set();

        // Listener untuk pesan yang dikirim admin (incoming)
        Echo.channel("chat-room").listen("AdminMessageSent", (event) => {
            if (event.message.target && String(event.message.target) === String(window.userId)) {
                appendMessageToChat(
                    event.message.message,
                    "admin",
                    event.message.timestamp,
                    true, {
                        target: event.message.target
                    }
                );
            } else {
                console.warn("Admin message target mismatch.", event.message.target, window.userId);
            }
        });

        // Listener untuk pesan yang dikirim user (incoming kembali; biasanya echo atas pengiriman sendiri)
        Echo.channel("chat-room").listen("UserMessageSent", (event) => {
            if (event.message.client_message_id && window.sentMessageIds.has(event.message.client_message_id)) {
                window.sentMessageIds.delete(event.message.client_message_id);
                return;
            }
            const payloadUserId = event.message.user_id || window.userId;
            if (String(payloadUserId) === String(window.userId)) {
                appendMessageToChat(
                    event.message.message,
                    "user",
                    event.message.timestamp,
                    true, {
                        user_id: payloadUserId,
                        user_name: event.message.user_name
                    }
                );
            }
        });

        // Listener untuk event "MessageRead"
        // Di sisi user, event dengan sender_type "user" menandakan bahwa pesan yang dikirim oleh user sudah dibaca oleh admin.
        Echo.channel("chat-room").listen("MessageRead", (event) => {
            // Tanpa console.log, langsung perbarui tampilan pesan.
            if (event.sender_type === 'user' && String(event.conversation.user_id) === String(window.userId)) {
                document.querySelectorAll("#chatMessages .user-message").forEach((el) => {
                    // Pilih <small> dalam container pesan (asumsi struktur: div > small)
                    let smallEl = el.querySelector("div > small");
                    if (smallEl && !smallEl.innerHTML.includes("Dilihat")) {
                        smallEl.innerHTML +=
                            ' <span class="badge bg-success ms-2 read-label">Dilihat</span>';
                    }
                });
            }
        });
    </script>


    @yield('script')

</body>

</html>
