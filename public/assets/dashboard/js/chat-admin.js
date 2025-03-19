// Fungsi helper untuk escaping karakter HTML yang berisiko
function escapeHTML(str) {
    if (!str) return "";
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

const getRelativeTime = (e) => {
    const now = new Date();
    const then = new Date(e);
    const secDiff = Math.floor((now - then) / 1000);
    const periods = [
        { label: "tahun", seconds: 31536000 },
        { label: "bulan", seconds: 2592000 },
        { label: "hari", seconds: 86400 },
        { label: "jam", seconds: 3600 },
        { label: "menit", seconds: 60 },
        { label: "detik", seconds: 1 },
    ];
    for (const period of periods) {
        const count = Math.floor(secDiff / period.seconds);
        if (count >= 1) return `${count} ${period.label} yang lalu`;
    }
    return "Baru saja";
};

function debounce(func, wait) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), wait);
    };
}

// Inisialisasi tabel chat user dengan DataTable
$(document).ready(() => {
    window.chatTable = $("#chatTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: "/get-chat-users",
        order: [],
        columns: [
            { data: "DT_RowIndex", orderable: false, searchable: false },
            { data: "nama_pengguna", name: "nama_pengguna" },
            { data: "pesan_terakhir", name: "pesan_terakhir" },
            { data: "waktu", name: "waktu" },
            { data: "aksi", orderable: false, searchable: false },
            { data: "unread", orderable: false, searchable: false },
        ],
    });
});

// Menandai pesan yang sudah dibaca saat scroll
$("#chatMessages").on(
    "scroll",
    debounce(function () {
        let $this = $(this);
        if ($this.scrollTop() + $this.innerHeight() >= this.scrollHeight - 50) {
            const conversation = $("#chatDetailModal").data("conversation");
            if (conversation && conversation.user_id) {
                fetch(`/mark-chat-read/${conversation.user_id}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                    .then((res) => res.json())
                    .then((data) => {
                        console.log("Mark as read:", data.message);
                    })
                    .catch((error) =>
                        console.error("Error marking messages read:", error)
                    );
            }
        }
    }, 500)
);

// Buka modal chat dengan pengguna tertentu
$(document).on("click", ".openChatModal", function () {
    const conversationData = $(this).attr("data-conversation");
    let conversation = {};
    try {
        conversation = JSON.parse(conversationData);
    } catch (error) {
        console.error("Gagal memparsing data conversation:", error);
    }
    // Menggunakan .text() untuk secara otomatis meng-escape konten teks
    $("#chatDetailModalLabel").text(
        `Chat dengan ${conversation.user_name || conversation.user_id}`
    );
    $("#chatDetailModal").data("conversation", conversation);
    $("#chatMessages").empty();
    loadMessagesForUser(conversation.user_id);
    loadTemplateTanyaJawab();
});

// Fungsi untuk refresh tabel chat tanpa reload halaman
const refreshChatTable = () => {
    if (window.chatTable) window.chatTable.ajax.reload(null, false);
};

/**
 * Fungsi appendMessageToChat
 * Kode berikut telah dimodifikasi agar:
 * - Pesan (atau property message pada objek) di-escape menggunakan escapeHTML.
 * - Nama pengguna juga di-escape apabila berasal dari input user.
 */
const appendMessageToChat = (
    messageData,
    senderRole,
    timestamp = new Date().toISOString(),
    shouldSave = true,
    extras = {}
) => {
    // Jika messageData merupakan objek, ambil property message, jika string langsung digunakan
    const rawMessage =
        typeof messageData === "object"
            ? messageData.message || ""
            : messageData;
    // Hasil sanitasi pesan
    const safeMessage = escapeHTML(rawMessage);

    // Jika role sama dengan currentRole dan pesan sudah terbaca, tampilkan label "Dilihat"
    let readLabel = "";
    if (
        window.currentRole &&
        window.currentRole === senderRole &&
        extras.read_at
    ) {
        readLabel =
            '<span class="badge bg-success ms-2 read-label">Dilihat</span>';
    }

    // Pastikan nama pengguna di-sanitasi
    const safeUserName = escapeHTML(extras.user_name || "Unknown");

    let htmlContent = "";
    if (senderRole === "admin") {
        htmlContent = `
        <div class="d-flex mb-2 justify-content-end admin-message">
          <div class="p-2 bg-light rounded" style="max-width:75%;">
            <small class="text-muted">Admin</small>
            <p class="mb-0">${safeMessage}</p>
            <small class="text-muted">${getRelativeTime(
                timestamp
            )} ${readLabel}</small>
          </div>
        </div>
      `;
    } else {
        htmlContent = `
        <div class="d-flex mb-2 user-message">
          <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">
            <small class="text-white">${safeUserName}</small>
            <p class="mb-0">${safeMessage}</p>
            <small class="text-white">${getRelativeTime(timestamp)}</small>
          </div>
        </div>
      `;
    }
    const $chatMessages = $("#chatMessages");
    $chatMessages.append(htmlContent);
    $chatMessages.scrollTop($chatMessages.prop("scrollHeight"));

    // Jika perlu menyimpan pesan ke localStorage
    if (shouldSave) {
        const msgData = {
            id: Date.now(),
            sender: senderRole,
            user_id:
                senderRole === "admin"
                    ? $("#chatDetailModal").data("conversation")?.user_id || ""
                    : extras.user_id || "",
            message: rawMessage, // Simpan pesan asli, bukan versi ter-sanitasi
            timestamp: timestamp,
            ...(extras.client_message_id
                ? { client_message_id: extras.client_message_id }
                : {}),
        };

        let savedMessages =
            JSON.parse(localStorage.getItem("chatMessages")) || [];
        const isDuplicate = extras.client_message_id
            ? savedMessages.some(
                  (m) => m.client_message_id === extras.client_message_id
              )
            : savedMessages.some(
                  (m) =>
                      m.sender === senderRole &&
                      m.message === rawMessage &&
                      Math.abs(new Date(m.timestamp) - new Date(timestamp)) <
                          2000
              );
        if (!isDuplicate) {
            savedMessages.push(msgData);
            localStorage.setItem("chatMessages", JSON.stringify(savedMessages));
        }
    }
};

const loadMessagesForUser = (userId) => {
    fetch(`/admin/messages/${userId}`, {
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
    })
        .then((res) => res.json())
        .then((messages) => {
            $("#chatMessages").empty();
            messages.forEach((msg) => {
                const senderType = msg.sender_type || msg.sender;
                const msgTimestamp = msg.timestamp || msg.created_at;
                // appendMessageToChat akan melakukan sanitasi di dalamnya
                appendMessageToChat(msg, senderType, msgTimestamp, false, {
                    user_id: msg.user_id,
                    user_name:
                        senderType === "admin"
                            ? "Admin"
                            : msg.user_name || msg.user_id,
                    client_message_id: msg.client_message_id,
                    read_at: msg.read_at,
                });
            });
        })
        .catch((error) =>
            console.error("Error loading messages for user:", error)
        );
};

const loadTemplateTanyaJawab = () => {
    fetch("/template_tanya_jawab")
        .then((res) => res.json())
        .then((templates) => {
            const $templateContainer = $("#templateTanyaJawab");
            $templateContainer.empty().append("<h6>Pertanyaan Template:</h6>");
            templates.forEach((template) => {
                // .text() secara otomatis meng-escape teks
                const $btn = $("<button></button>")
                    .addClass("btn btn-outline-primary m-1")
                    .text(template.pertanyaan);
                $btn.on("click", () => {
                    const conversation =
                        $("#chatDetailModal").data("conversation") || {};
                    const now = new Date().toISOString();
                    appendMessageToChat(
                        template.pertanyaan,
                        "user",
                        now,
                        true,
                        {
                            user_id: conversation.user_id,
                            user_name: conversation.user_name,
                        }
                    );
                    fetch("/send-user-message", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        body: JSON.stringify({
                            message: template.pertanyaan,
                            user_id: conversation.user_id,
                        }),
                    })
                        .then((res) => res.json())
                        .then((data) =>
                            console.log("Pertanyaan terkirim:", data.message)
                        )
                        .catch((error) =>
                            console.error("Error mengirim pertanyaan:", error)
                        );

                    setTimeout(() => {
                        fetch("/send-admin-message", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                            body: JSON.stringify({
                                message: template.jawaban,
                                target: conversation.user_id,
                            }),
                        })
                            .then((res) => res.json())
                            .then((data) =>
                                console.log("Jawaban terkirim:", data.message)
                            )
                            .catch((err) => {
                                console.error("Error mengirim jawaban:", err);
                                appendMessageToChat(
                                    template.jawaban,
                                    "admin",
                                    new Date().toISOString(),
                                    true,
                                    {
                                        target: conversation.user_id,
                                        template: true,
                                    }
                                );
                            });
                    }, 1000);
                });
                $templateContainer.append($btn);
            });
        })
        .catch((error) => console.error("Error fetching templates:", error));
};

// Event submission form chat admin
document
    .querySelector("#chatDetailModal form")
    .addEventListener("submit", function (e) {
        e.preventDefault();
        const replyInput = this.querySelector('input[name="reply"]');
        const replyMessage = replyInput.value.trim();
        if (replyMessage !== "") {
            const conversation =
                $("#chatDetailModal").data("conversation") || {};
            const targetUserId = conversation.user_id;
            const clientMessageId = `${Date.now()}_${Math.random()
                .toString(36)
                .substring(2, 10)}`;
            window.adminSentMessageIds.add(clientMessageId);
            fetch("/send-admin-message", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    message: replyMessage,
                    target: targetUserId,
                    client_message_id: clientMessageId,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    console.log(data.message);
                    replyInput.value = "";
                    appendMessageToChat(
                        {
                            message: replyMessage,
                            client_message_id: clientMessageId,
                        },
                        "admin",
                        new Date().toISOString(),
                        true,
                        {
                            target: targetUserId,
                            client_message_id: clientMessageId,
                        }
                    );
                    if (window.chatTable)
                        window.chatTable.ajax.reload(null, false);
                })
                .catch((error) => console.error("Error:", error));
        }
    });

// Blur fokus saat modal chat detail dihilangkan
$("#chatDetailModal").on("hidden.bs.modal", function () {
    $(this).find(":focus").blur();
    refreshChatTable();
});

$("#chatDetailModal").on("shown.bs.modal", () => {
    $('#chatInput').trigger('focus');
});
