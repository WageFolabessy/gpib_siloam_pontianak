const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

const getRelativeTime = (timestamp) => {
    const now = new Date();
    const time = new Date(timestamp);
    const diff = Math.floor((now - time) / 1000);
    const intervals = [
        { label: "tahun", seconds: 31536000 },
        { label: "bulan", seconds: 2592000 },
        { label: "hari", seconds: 86400 },
        { label: "jam", seconds: 3600 },
        { label: "menit", seconds: 60 },
        { label: "detik", seconds: 1 },
    ];
    for (const interval of intervals) {
        const count = Math.floor(diff / interval.seconds);
        if (count >= 1) return `${count} ${interval.label} yang lalu`;
    }
    return "Baru saja";
};

$(document).ready(() => {
    window.chatTable = $("#chatTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: "/get-chat-users",
        order: [],
        columns: [
            {
                data: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            {
                data: "nama_pengguna",
                name: "nama_pengguna",
            },
            {
                data: "pesan_terakhir",
                name: "pesan_terakhir",
            },
            {
                data: "waktu",
                name: "waktu",
            },
            {
                data: "aksi",
                orderable: false,
                searchable: false,
            },
        ],
    });
});

function debounce(func, delay) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}
// Event listener pada scroll container pesan.
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
                    .then((response) => response.json())
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

$(document).on("click", ".openChatModal", function () {
    const rawConversation = $(this).attr("data-conversation");
    let conversation = {};
    try {
        conversation = JSON.parse(rawConversation);
    } catch (e) {
        console.error("Gagal memparsing data conversation:", e);
    }
    $("#chatDetailModalLabel").text(
        `Chat dengan ${conversation.user_name || conversation.user_id}`
    );
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

const appendMessageToChat = (
    message,
    sender,
    timestamp = new Date().toISOString(),
    shouldStore = true,
    meta = {}
) => {
    const text = typeof message === "object" ? message.message || "" : message;

    let readLabel = "";
    if (window.currentRole && window.currentRole === sender && meta.read_at) {
        readLabel = `<span class="badge bg-success ms-2 read-label">Dilihat</span>`;
    }

    let messageHtml = "";
    if (sender === "admin") {
        // Tambahkan kelas "admin-message" untuk memudahkan update nanti.
        messageHtml = `
                <div class="d-flex mb-2 justify-content-end admin-message">
                    <div class="p-2 bg-light rounded" style="max-width:75%;">
                        <small class="text-muted">Admin</small>
                        <p class="mb-0">${text}</p>
                        <small class="text-muted">${getRelativeTime(
                            timestamp
                        )} ${readLabel}</small>
                    </div>
                </div>
            `;
    } else {
        messageHtml = `
                <div class="d-flex mb-2 user-message">
                    <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">
                        <small class="text-white">${
                            meta.user_name || "Unknown"
                        }</small>
                        <p class="mb-0">${text}</p>
                        <small class="text-white">${getRelativeTime(
                            timestamp
                        )}</small>
                    </div>
                </div>
            `;
    }

    const $chatMessages = $("#chatMessages");
    $chatMessages.append(messageHtml);
    $chatMessages.scrollTop($chatMessages.prop("scrollHeight"));

    if (shouldStore) {
        const newMessage = {
            id: Date.now(),
            sender: sender,
            user_id:
                sender === "admin"
                    ? $("#chatDetailModal").data("conversation")?.user_id || ""
                    : meta.user_id || "",
            message: text,
            timestamp: timestamp,
            ...(meta.client_message_id
                ? { client_message_id: meta.client_message_id }
                : {}),
        };
        let storedMessages =
            JSON.parse(localStorage.getItem("chatMessages")) || [];
        const duplicate = meta.client_message_id
            ? storedMessages.some(
                  (m) => m.client_message_id === meta.client_message_id
              )
            : storedMessages.some(
                  (m) =>
                      m.sender === sender &&
                      m.message === text &&
                      Math.abs(new Date(m.timestamp) - new Date(timestamp)) <
                          2000
              );
        if (!duplicate) {
            storedMessages.push(newMessage);
            localStorage.setItem(
                "chatMessages",
                JSON.stringify(storedMessages)
            );
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
        .then((response) => response.json())
        .then((messages) => {
            $("#chatMessages").empty();
            messages.forEach((message) => {
                const sender = message.sender_type || message.sender;
                // Gunakan timestamp dari field created_at atau custom timestamp yang sudah dikonversi di server.
                const timestamp = message.timestamp || message.created_at;
                appendMessageToChat(message, sender, timestamp, false, {
                    user_id: message.user_id,
                    user_name:
                        sender === "admin"
                            ? "Admin"
                            : message.user_name || message.user_id,
                    client_message_id: message.client_message_id,
                    read_at: message.read_at,
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
            const container = $("#templateTanyaJawab");
            container.empty().append("<h6>Pertanyaan Template:</h6>");
            templates.forEach((template) => {
                const btn = $("<button></button>")
                    .addClass("btn btn-outline-primary m-1")
                    .text(template.pertanyaan);
                btn.on("click", () => {
                    const conversation =
                        $("#chatDetailModal").data("conversation") || {};
                    const nowStr = new Date().toISOString();
                    appendMessageToChat(
                        template.pertanyaan,
                        "user",
                        nowStr,
                        true,
                        {
                            user_id: conversation.user_id,
                            user_name: conversation.user_name,
                            template: true,
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
                        .catch((err) =>
                            console.error("Error mengirim pertanyaan:", err)
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
                container.append(btn);
            });
        })
        .catch((err) => console.error("Error fetching templates:", err));
};

// Penanganan submit form reply admin
document
    .querySelector("#chatDetailModal form")
    .addEventListener("submit", function (e) {
        e.preventDefault();
        const replyInput = this.querySelector('input[name="reply"]');
        const replyMsg = replyInput.value.trim();
        if (replyMsg !== "") {
            const conversation =
                $("#chatDetailModal").data("conversation") || {};
            const targetUserId = conversation.user_id;
            const clientMsgId = `${Date.now()}_${Math.random()
                .toString(36)
                .substring(2, 10)}`;
            window.adminSentMessageIds.add(clientMsgId);
            fetch("/send-admin-message", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    message: replyMsg,
                    target: targetUserId,
                    client_message_id: clientMsgId,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    console.log(data.message);
                    replyInput.value = "";
                    appendMessageToChat(
                        { message: replyMsg, client_message_id: clientMsgId },
                        "admin",
                        new Date().toISOString(),
                        true,
                        { target: targetUserId, client_message_id: clientMsgId }
                    );
                    if (window.chatTable) {
                        window.chatTable.ajax.reload(null, false);
                    }
                })
                .catch((err) => console.error("Error:", err));
        }
    });

$("#chatDetailModal").on("hidden.bs.modal", function () {
    $(this).find(":focus").blur();
});
