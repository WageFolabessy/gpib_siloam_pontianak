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
        $(document).ready(() => {
            window.chatTable = $('#chatTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('chat.users') }}',
                order: [],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_pengguna',
                        name: 'nama_pengguna'
                    },
                    {
                        data: 'pesan_terakhir',
                        name: 'pesan_terakhir'
                    },
                    {
                        data: 'waktu',
                        name: 'waktu'
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        $(document).on("click", ".openChatModal", function() {
            const rawConversation = $(this).attr('data-conversation');
            let conversation = {};
            try {
                conversation = JSON.parse(rawConversation);
            } catch (e) {
                console.error("Gagal memparsing data conversation:", e);
            }
            $("#chatDetailModalLabel").text(`Chat dengan ${conversation.user_name || conversation.user_id}`);
            $("#chatDetailModal").data("conversation", conversation);
            $("#chatMessages").empty();
            loadMessagesForUser(conversation.user_id);
            loadTemplateTanyaJawab();
        });

        // Fungsi untuk me-refresh DataTables
        const refreshChatTable = () => {
            if (window.chatTable) {
                window.chatTable.ajax.reload(null, false);
            }
        };

        // Global variable untuk menghindari duplikasi broadcast pesan admin
        window.adminSentMessageIds = new Set();

        // Listener event broadcast pesan admin
        Echo.channel("chat-room").listen("AdminMessageSent", (e) => {
            console.log("AdminMessageSent received:", e.message);
            // Dapatkan conversation aktif dari modal, jika ada
            const conversation = $("#chatDetailModal").data("conversation");
            if (conversation && conversation.user_id && e.message.target && String(e.message.target) === String(
                    conversation.user_id)) {
                if (e.message.client_message_id && window.adminSentMessageIds.has(e.message.client_message_id)) {
                    console.log("Skipping duplicate admin broadcast:", e.message.client_message_id);
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
            console.log("UserMessageSent received:", e.message);
            // Ambil conversation aktif; jika tidak ada (modal tidak terbuka),
            // maka jangan coba append pesan ke modal detail
            const conversation = $("#chatDetailModal").data("conversation");
            if (!conversation || !conversation.user_id) {
                refreshChatTable();
                return;
            }
            // Jika pesan datang dari user yang sesuai dengan conversation aktif, tampilkan di modal
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
                console.warn(
                    "Pesan dari user tidak ditampilkan karena tidak sesuai dengan conversation aktif.",
                    "Pesan user_id:", e.message.user_id,
                    "Conversation aktif user_id:", conversation.user_id
                );
            }
            refreshChatTable();
        });
    </script>
@endsection
