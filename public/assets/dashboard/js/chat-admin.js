// Fungsi untuk menghitung waktu relatif
function getRelativeTime(timestamp) {
    const now = new Date();
    const then = new Date(timestamp);
    const seconds = Math.floor((now - then) / 1000);
    const intervals = [
        { label: "tahun", seconds: 31536000 },
        { label: "bulan", seconds: 2592000 },
        { label: "hari", seconds: 86400 },
        { label: "jam", seconds: 3600 },
        { label: "menit", seconds: 60 },
        { label: "detik", seconds: 1 },
    ];
    for (let i = 0; i < intervals.length; i++) {
        const interval = intervals[i];
        const count = Math.floor(seconds / interval.seconds);
        if (count >= 1) return count + " " + interval.label + " yang lalu";
    }
    return "Baru saja";
}

// Fungsi untuk mengelompokkan pesan berdasarkan user_id (tanpa filter sender)
function getConversations() {
    let messages = JSON.parse(localStorage.getItem("chatMessages")) || [];
    let conversations = {};
    messages.forEach(function (message) {
        // Jika pesan memiliki properti user_id, kelompokkan berdasarkan user_id
        if (message.user_id) {
            let key = message.user_id;
            if (!conversations[key]) {
                conversations[key] = {
                    user_id: key,
                    lastMessage: message.message,
                    timestamp: message.timestamp,
                    messages: [],
                };
            }
            conversations[key].messages.push(message);
            // Perbarui percakapan jika pesan ini lebih baru
            if (
                new Date(message.timestamp) >
                new Date(conversations[key].timestamp)
            ) {
                conversations[key].lastMessage = message.message;
                conversations[key].timestamp = message.timestamp;
            }
        }
    });
    return conversations;
}

let chatTable;
function loadChatTable() {
    const conversations = getConversations();
    let convArray = Object.values(conversations);
    // Urutkan percakapan secara descending berdasarkan timestamp (pesan terbaru paling atas)
    convArray.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

    let data = [];
    let no = 1;
    convArray.forEach((conv) => {
        const lastMsg = conv.lastMessage;
        data.push([
            no++,
            conv.user_id,
            String(lastMsg).substring(0, 50),
            getRelativeTime(conv.timestamp),
            `<a href="#" class="btn btn-primary btn-sm openChatModal" data-bs-toggle="modal"
                data-bs-target="#chatDetailModal" data-conversation='${JSON.stringify(
                    { user_id: conv.user_id }
                )}'>
                Lihat Chat
            </a>`,
        ]);
    });
    if (chatTable) {
        chatTable.clear().rows.add(data).draw();
    } else {
        chatTable = $("#chatTable").DataTable({
            data: data,
            columns: [
                { title: "No" },
                { title: "Nama Pengguna" },
                { title: "Pesan Terakhir" },
                { title: "Waktu" },
                { title: "Aksi" },
            ],
        });
    }
}

/**
 * Fungsi untuk menambahkan pesan ke chat dengan cek duplikat.
 * Revisi pada bagian ini: jika pesan yang diterima adalah dari user, kita ambil user_id langsung dari payload.
 */
function appendMessageToChat(msgContent, senderType) {
    // Jika msgContent adalah objek, ambil nilai message dan user_id (jika ada)
    let messageText = "";
    let conversationId = "";
    if (typeof msgContent === "object") {
        messageText = msgContent.message || "";
        if (senderType === "user" && msgContent.user_id) {
            conversationId = msgContent.user_id;
        }
    } else {
        messageText = msgContent;
    }
    // Untuk pesan admin, ambil user_id dari data conversation di modal
    if (senderType === "admin") {
        conversationId =
            $("#chatDetailModal").data("conversation")?.user_id || "";
    }

    let nowISO = new Date().toISOString();

    // Buat objek pesan baru yang selalu menyertakan user_id
    let newMessage = {
        id: Date.now(),
        sender: senderType,
        user_id: conversationId,
        message: messageText,
        timestamp: nowISO,
    };

    let messages = JSON.parse(localStorage.getItem("chatMessages")) || [];

    // Cek duplikasi berdasarkan sender, message, dan timestamp (toleransi 2 detik)
    let isDuplicate = messages.some((msg) => {
        return (
            msg.sender === newMessage.sender &&
            msg.message === newMessage.message &&
            Math.abs(new Date(msg.timestamp) - new Date(newMessage.timestamp)) <
                2000
        );
    });

    if (!isDuplicate) {
        messages.push(newMessage);
        localStorage.setItem("chatMessages", JSON.stringify(messages));
        loadChatTable();
    }

    // Jika modal chat terbuka, tampilkan pesan di area chat jika sesuai dengan conversation aktif
    if ($("#chatDetailModal").hasClass("show")) {
        let currentConversation =
            $("#chatDetailModal").data("conversation")?.user_id;
        if (currentConversation && currentConversation === newMessage.user_id) {
            let chatMessagesDiv = $("#chatMessages");
            if (senderType === "admin") {
                chatMessagesDiv.append(`
                    <div class="d-flex mb-2 justify-content-end">
                        <div class="p-2 bg-light rounded" style="max-width:75%;">
                            <small class="text-muted float-end">Admin</small>
                            <p class="mb-0">${messageText}</p>
                            <small class="text-muted float-end">${getRelativeTime(
                                newMessage.timestamp
                            )}</small>
                        </div>
                    </div>
                `);
            } else {
                chatMessagesDiv.append(`
                    <div class="d-flex mb-2">
                        <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">
                            <small class="text-white">User</small>
                            <p class="mb-0">${messageText}</p>
                            <small class="text-white">${getRelativeTime(
                                newMessage.timestamp
                            )}</small>
                        </div>
                    </div>
                `);
            }
        }
    }
}

let suppressAdminBroadcast = false;

$(document).ready(function () {
    loadChatTable();

    // Buka modal chat detail ketika tombol "Lihat Chat" diklik
    $(document).on("click", ".openChatModal", function () {
        // Ambil data conversation dari data attribute (berupa JSON string)
        var conversationData = JSON.parse($(this).attr("data-conversation"));
        $("#chatDetailModalLabel").text(
            "Chat dengan " + conversationData.user_id
        );
        // Simpan data conversation ke modal
        $("#chatDetailModal").data("conversation", conversationData);
        let chatMessagesDiv = $("#chatMessages");
        chatMessagesDiv.empty();
        const conv = getConversations()[conversationData.user_id];
        if (conv && conv.messages) {
            conv.messages.forEach(function (msg) {
                const messageContent =
                    typeof msg.message === "object" && msg.message.message
                        ? msg.message.message
                        : msg.message;
                if (msg.sender === "admin") {
                    chatMessagesDiv.append(`
                        <div class="d-flex mb-2 justify-content-end">
                            <div class="p-2 bg-light rounded" style="max-width:75%;">
                                <small class="text-muted float-end">Admin</small>
                                <p class="mb-0">${messageContent}</p>
                                <small class="text-muted float-end">${getRelativeTime(
                                    msg.timestamp
                                )}</small>
                            </div>
                        </div>
                    `);
                } else {
                    chatMessagesDiv.append(`
                        <div class="d-flex mb-2">
                            <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">
                                <small class="text-white">User</small>
                                <p class="mb-0">${messageContent}</p>
                                <small class="text-white">${getRelativeTime(
                                    msg.timestamp
                                )}</small>
                            </div>
                        </div>
                    `);
                }
            });
        }
    });
});

// Event handler untuk form chat detail (pengiriman pesan admin)
document
    .querySelector("#chatDetailModal form")
    .addEventListener("submit", function (e) {
        e.preventDefault();
        const inputReply = this.querySelector('input[name="reply"]');
        const replyText = inputReply.value.trim();
        if (replyText !== "") {
            suppressAdminBroadcast = true;
            // Ambil data conversation dari modal (harus selalu berisi user_id)
            const conversationData = $("#chatDetailModal").data("conversation");
            const targetUserId = conversationData.user_id; // pakai user_id saja
            fetch(
                "/send-admin-message?message=" +
                    encodeURIComponent(replyText) +
                    "&target=" +
                    encodeURIComponent(targetUserId)
            )
                .then((response) => response.json())
                .then((data) => {
                    console.log(data.message);
                    inputReply.value = "";
                    // Simpan pesan admin ke localStorage dan tampilkan di chat modal
                    // Catatan: appendMessageToChat() secara otomatis membuat timestamp baru
                    appendMessageToChat(
                        { message: replyText, user_id: targetUserId },
                        "admin"
                    );
                })
                .catch((error) => console.error("Error:", error))
                .finally(() => {
                    setTimeout(() => {
                        suppressAdminBroadcast = false;
                    }, 1500);
                });
        }
    });
