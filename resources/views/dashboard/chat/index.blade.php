@extends('dashboard.components.main')
@section('title')
    - Chat
@endsection

@section('content')
    <div class="card mb-4 shadow">
        <div class="card-header py-3">
            <h6 class="font-weight-bold m-0 text-primary">Daftar Chat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="chatTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Pesan Terakhir</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="chatTableBody"></tbody>
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
        window.currentRole = 'admin';
        window.adminSentMessageIds = new Set();

        // Listener event broadcast pesan admin
        Echo.channel("chat-room").listen("AdminMessageSent", (e) => {
            // Ambil conversation aktif dari modal detail
            const conversation = $("#chatDetailModal").data("conversation");
            if (conversation && conversation.user_id && e.message.target && String(e.message.target) === String(
                    conversation.user_id)) {
                if (e.message.client_message_id && window.adminSentMessageIds.has(e.message.client_message_id)) {
                    window.adminSentMessageIds.delete(e.message.client_message_id);
                    return;
                }
                appendMessageToChat(
                    e.message.message,
                    "admin",
                    e.message.timestamp,
                    true, {
                        target: e.message.target,
                        client_message_id: e.message.client_message_id
                    }
                );
            }
            refreshChatTable();
        });

        // Listener event broadcast pesan user
        Echo.channel("chat-room").listen("UserMessageSent", (e) => {
            const conversation = $("#chatDetailModal").data("conversation");
            if (!conversation || !conversation.user_id) {
                refreshChatTable();
                return;
            }
            if (String(e.message.user_id) === String(conversation.user_id)) {
                appendMessageToChat(
                    e.message.message,
                    "user",
                    e.message.timestamp,
                    true, {
                        user_id: e.message.user_id,
                        user_name: e.message.user_name
                    }
                );
            } else {
                console.warn("Pesan dari user tidak ditampilkan karena tidak sesuai dengan conversation aktif.",
                    "Pesan user_id:", e.message.user_id,
                    "Conversation aktif user_id:", conversation.user_id);
            }
            refreshChatTable();
        });

        // Listener untuk event "MessageRead"
        Echo.channel("chat-room").listen("MessageRead", (e) => {
            // Pastikan event hanya diproses untuk pesan admin yang sudah dibaca oleh user
            const conversation = $("#chatDetailModal").data("conversation");
            if (conversation &&
                conversation.user_id &&
                e.sender_type === "admin" &&
                String(conversation.user_id) === String(e.conversation.user_id)
            ) {
                $("#chatMessages .admin-message").each(function() {
                    // Ambil elemen <small> terakhir (diasumsikan berisi waktu)
                    const $timestampSmall = $(this).find("small.text-muted").last();
                    // Hapus badge "read-label" yang sudah ada
                    $timestampSmall.find(".read-label").remove();
                    // Tambahkan badge "Dilihat" hanya jika belum ada
                    if ($timestampSmall.children(".read-label").length === 0) {
                        $timestampSmall.append(
                            ' <span class="badge bg-success ms-2 read-label">Dilihat</span>');
                    }
                });
            }
        });
    </script>
@endsection
  