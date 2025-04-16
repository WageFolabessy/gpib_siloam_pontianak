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
        }

        #chatCount {
            font-size: 0.7rem;
            line-height: 1;
            padding: 0.25em 0.5em;
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

    {{-- Tombol Chat Fixed --}}
    <button type="button" class="btn btn-primary rounded-circle" id="chatButton" data-bs-toggle="modal"
        data-bs-target="#chatModal" aria-label="Buka Chat">
        <i class="fas fa-comments" style="font-size:24px"></i>
        <span id="chatCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="display: none;">0</span>
    </button>

    {{-- Include Modal Chat --}}
    @include('chat.modal-chat')

    <script src="{{ asset('assets/dashboard/vendor/jquery/jquery-3.7.0.min.js') }}"></script>

    <script src="{{ asset('assets/pages/js/bootstrap.bundle.min.js') }}"></script>

    @vite('resources/js/app.js')

    <script src="{{ asset('assets/pages/js/speechsynthesis/index.js') }}"></script>
    <script src="{{ asset('assets/pages/js/speechsynthesis/tsa.js') }}"></script>
    <script src="{{ asset('assets/pages/js/chat/chat-user.js') }}"></script>

    <script type="module">
        @if (Auth::check())
            window.userId = "{{ Auth::user()->id }}";
            window.userName = "{{ Auth::user()->name }}";
            window.currentRole = "user";
        @else
            let tempUserId = localStorage.getItem("userId");
            if (!tempUserId) {
                tempUserId = "guest_" + Math.random().toString(36).substring(2, 12);
                localStorage.setItem("userId", tempUserId);
            }
            window.userId = tempUserId;
            window.userName = "Tamu";
            window.currentRole = "guest";
        @endif

        window.sentMessageIds = new Set();

        if (typeof Echo !== 'undefined') {
            Echo.channel("chat-room").listen("AdminMessageSent", (event) => {
                if (event.message.target && String(event.message.target) === String(window.userId)) {
                    if (typeof appendMessageToChat === 'function') {
                        appendMessageToChat(
                            event.message.message,
                            "admin",
                            event.message.timestamp,
                            true, {
                                target: event.message.target
                            }
                        );
                    } else {
                        console.error("appendMessageToChat function not found.");
                    }
                }
            });

            Echo.channel("chat-room").listen("UserMessageSent", (event) => {
                if (event.message.client_message_id && window.sentMessageIds.has(event.message.client_message_id)) {
                    window.sentMessageIds.delete(event.message.client_message_id);
                    return;
                }
                const payloadUserId = event.message.user_id;
                if (payloadUserId && String(payloadUserId) === String(window.userId)) {
                    if (typeof appendMessageToChat === 'function') {
                        appendMessageToChat(
                            event.message.message,
                            "user",
                            event.message.timestamp,
                            true, {
                                user_id: payloadUserId,
                                user_name: event.message.user_name
                            }
                        );
                    } else {
                        console.error("appendMessageToChat function not found.");
                    }
                }
            });

            Echo.channel("chat-room").listen("MessageRead", (event) => {
                if (event.sender_type === 'user' && event.conversation && String(event.conversation.user_id) ===
                    String(window.userId)) {
                    document.querySelectorAll("#chatMessages .user-message:not(.read)").forEach((el) => {
                        let smallEl = el.querySelector("div > small");
                        if (smallEl) {
                            if (!smallEl.querySelector('.read-label')) {
                                smallEl.innerHTML +=
                                    ' <span class="badge bg-success ms-1 read-label">Dilihat</span>';
                            }
                            el.classList.add('read');
                        }
                    });
                }
            });

        } else {
            console.warn("Laravel Echo is not defined. Real-time chat features might not work.");
        }
    </script>

    @yield('script')

</body>

</html>
