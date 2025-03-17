const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

const getRelativeTime = (timestamp) => {
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
    for (const interval of intervals) {
        const count = Math.floor(seconds / interval.seconds);
        if (count >= 1) return `${count} ${interval.label} yang lalu`;
    }
    return "Baru saja";
};

const appendMessageToChat = (
    data,
    sender,
    timestamp = new Date().toISOString(),
    autoSave = true,
    opts = {}
) => {
    // Jika data bertipe object (payload pesan) gunakan properti message
    const msg = typeof data === "object" ? data.message || "" : data;
    let html = "";
    if (sender === "admin") {
        html = `
            <div class="d-flex mb-2 justify-content-end">
                <div class="p-2 bg-light rounded" style="max-width:75%;">
                    <small class="text-muted">Admin</small>
                    <p class="mb-0">${msg}</p>
                    <small class="text-muted">${getRelativeTime(
                        timestamp
                    )}</small>
                </div>
            </div>
        `;
    } else {
        html = `
            <div class="d-flex mb-2">
                <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">
                    <small class="text-white">${
                        opts.user_name || "Unknown"
                    }</small>
                    <p class="mb-0">${msg}</p>
                    <small class="text-white">${getRelativeTime(
                        timestamp
                    )}</small>
                </div>
            </div>
        `;
    }
    const container = $("#chatMessages");
    container.append(html);
    container.scrollTop(container.prop("scrollHeight"));

    // Jika autoSave true, simpan ke localStorage dan update daftar chat (jika diperlukan)
    if (autoSave) {
        const messageObj = {
            id: Date.now(),
            sender: sender,
            user_id:
                sender === "admin"
                    ? $("#chatDetailModal").data("conversation")?.user_id || ""
                    : opts.user_id || "",
            message: msg,
            timestamp: timestamp,
        };
        if (opts.client_message_id) {
            messageObj.client_message_id = opts.client_message_id;
        }
        // Simpan message ke localStorage (opsional, sesuai kebutuhan)
        let messages = JSON.parse(localStorage.getItem("chatMessages")) || [];
        // Cegah duplikasi pesan
        const exists = opts.client_message_id
            ? messages.some((m) => m.message_id === opts.client_message_id)
            : messages.some(
                  (m) =>
                      m.sender === sender &&
                      m.message === msg &&
                      Math.abs(new Date(m.timestamp) - new Date(timestamp)) <
                          2000
              );
        if (!exists) {
            messages.push(messageObj);
            localStorage.setItem("chatMessages", JSON.stringify(messages));
            // Bila ingin mengupdate DataTables, panggil fungsi loadChatTable() (jika menggunakan data client-side)
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
                // Gantikan msg.sender dengan msg.sender_type
                const sender = msg.sender_type || msg.sender;
                const messageTime = msg.timestamp || msg.created_at;
                appendMessageToChat(msg, sender, messageTime, false, {
                    user_id: msg.user_id,
                    user_name:
                        sender === "admin"
                            ? "Admin"
                            : msg.user_name || msg.user_id,
                    client_message_id: msg.client_message_id,
                });
            });
        })
        .catch((err) => console.error("Error loading messages for user:", err));
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
