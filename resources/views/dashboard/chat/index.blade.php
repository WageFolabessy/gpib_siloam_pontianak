@extends('dashboard.components.main')
@section('title')
    - Chat
@endsection
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Chat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="chatTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Pesan Terakhir</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="chatTableBody">
                        <!-- Data percakapan akan di-load melalui JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('dashboard.chat.modal-detail-chat')
@endsection

@section('script')
    @vite('resources/js/app.js')
    <script src="{{ asset('assets/dashboard/js/chat-admin.js') }}"></script>
    <script type="module">
        // Mendengarkan broadcast pesan dari channel 'chat-room'
        Echo.channel('chat-room').listen('AdminMessageSent', (e) => {
            if (suppressAdminBroadcast) return;
            console.log("Pesan admin:", e.message);
            appendMessageToChat(e.message, 'admin', e.message.timestamp);
        });
        Echo.channel('chat-room').listen('UserMessageSent', (e) => {
            console.log("Pesan user:", e.message);
            appendMessageToChat(e.message, 'user', e.message.timestamp);
        });
    </script>
@endsection
