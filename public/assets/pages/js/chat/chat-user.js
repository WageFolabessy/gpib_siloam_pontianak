// Pastikan window.userId sudah ada; jika belum, ambil dari localStorage.
if (window.userId === undefined) {
    let storedUserId = localStorage.getItem("userId");
    if (!storedUserId) {
        storedUserId = "user_" + Math.random().toString(36).substring(2, 10);
        localStorage.setItem("userId", storedUserId);
    }
    window.userId = storedUserId;
}

const getRelativeTime = (dateStr) => {
    const now = new Date();
    const past = new Date(dateStr);
    const secondsDiff = Math.floor((now - past) / 1000);
    const intervals = [
        { label: "tahun", seconds: 31536000 },
        { label: "bulan", seconds: 2592000 },
        { label: "hari", seconds: 86400 },
        { label: "jam", seconds: 3600 },
        { label: "menit", seconds: 60 },
        { label: "detik", seconds: 1 },
    ];
    for (const interval of intervals) {
        const count = Math.floor(secondsDiff / interval.seconds);
        if (count >= 1) {
            return `${count} ${interval.label} yang lalu`;
        }
    }
    return "Baru saja";
};

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

function debounce(func, delay) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

let chatMessagesDiv = document.getElementById("chatMessages");

function markMessageAsRead(userId, elem) {
    fetch(`/mark-chat-read/${userId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            // Meski console.log telah dihapus, kita tetap melakukan update tampilan.
            if (elem) {
                elem.textContent = "Dilihat";
                elem.style.pointerEvents = "none";
            }
        })
        .catch((error) => {
            console.error("Error marking message as read:", error);
        });
}

function saveMessage(messageObj) {
    const messages = JSON.parse(localStorage.getItem("chatMessages")) || [];
    messages.push(messageObj);
    localStorage.setItem("chatMessages", JSON.stringify(messages));
}

function appendMessageToChat(
    e,
    t,
    a = new Date().toISOString(),
    s = true,
    n = {}
) {
    const chatMessagesEl = document.getElementById("chatMessages");
    let messageWrapper = document.createElement("div");
    let readLabel = "";

    if (n.read_at) {
        readLabel =
            ' <span class="badge bg-success ms-2 read-label">Dilihat</span>';
    }

    // Lakukan sanitasi terhadap pesan yang di-display
    let safeMessage = escapeHTML(e);

    if ("admin" === t) {
        messageWrapper.classList.add("d-flex", "mb-2", "admin-message");
        messageWrapper.innerHTML = `
        <span class="fw-bold me-2" style="min-width:70px">Admin</span>
        <div class="p-2 bg-light rounded" style="max-width:75%;">
            ${safeMessage}
            <br>
            <small>${getRelativeTime(a)}</small>
        </div>
      `;
    } else {
        messageWrapper.classList.add(
            "d-flex",
            "mb-2",
            "flex-row-reverse",
            "user-message"
        );
        // Sanitasi nama pengguna juga, jika berasal dari input user
        const safeUserName = escapeHTML("Anda" || "Anda");
        messageWrapper.innerHTML = `
        <span class="fw-bold ms-2" style="min-width:70px">${safeUserName}</span>
        <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">
            ${safeMessage}
            <br>
            <small>${getRelativeTime(a)}${readLabel}</small>
        </div>
      `;
    }

    chatMessagesEl.appendChild(messageWrapper);
    chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;

    // Jika diperlukan, simpan pesan ke localStorage
    if (s) {
        const messageData = {
            message_id: n.client_message_id || Date.now(),
            sender: t,
            message: e, // Simpan pesan asli jika diperlukan
            timestamp: a,
            ...n,
        };

        let localMessages =
            JSON.parse(localStorage.getItem("chatMessages")) || [];

        // Cek duplikasi pesan
        const isDuplicate = n.client_message_id
            ? localMessages.some(
                  (msg) => msg.message_id === n.client_message_id
              )
            : localMessages.some(
                  (msg) =>
                      msg.sender === t &&
                      msg.message === e &&
                      Math.abs(new Date(msg.timestamp) - new Date(a)) < 2000
              );

        if (!isDuplicate) {
            localMessages.push(messageData);
            localStorage.setItem("chatMessages", JSON.stringify(localMessages));
        }
    }
}

function loadChatMessages() {
    fetch("/user/messages", {
        method: "GET",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
    })
        .then((response) => response.json())
        .then((messages) => {
            const chatMessagesEl = document.getElementById("chatMessages");
            chatMessagesEl.innerHTML = "";

            messages.forEach((msg) => {
                const sender = msg.sender_type === "admin" ? "admin" : "user";
                const safeMessage = escapeHTML(msg.message);
                const safeUserName = escapeHTML(
                    sender === "admin" ? "Admin" : msg.user_name
                );
                appendMessageToChat(
                    safeMessage,
                    sender,
                    msg.created_at,
                    false,
                    {
                        user_id: msg.user_id,
                        user_name: safeUserName,
                        client_message_id: msg.client_message_id,
                        read_at: msg.read_at,
                    }
                );
            });
        })
        .catch((error) => {
            console.error("Error loading chat messages:", error);
        });
}

// Fungsi untuk mengambil pesan dan menghitung jumlah pesan admin yang belum dibaca
function updateUnreadBadge() {
    fetch("/user/messages", {
        method: "GET",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
    })
        .then((response) => response.json())
        .then((messages) => {
            // Hitung pesan dari admin yang belum dibaca
            let unreadCount = messages.filter(
                (msg) => msg.sender_type === "admin" && !msg.read_at
            ).length;

            const badge = document.getElementById("chatCount");
            badge.textContent = unreadCount;
            badge.style.display = unreadCount > 0 ? "inline-block" : "none";
        })
        .catch((error) => {
            console.error("Error updating unread badge:", error);
        });
}

// Panggil updateUnreadBadge secara periodik (misalnya setiap 5 detik)
setInterval(updateUnreadBadge, 3000);

function sendUserMessage() {
    const chatInputElem = document.getElementById("chatInput");
    const text = chatInputElem.value.trim();
    if (text !== "") {
        const msgId =
            Date.now() + "_" + Math.random().toString(36).substring(2, 10);
        window.sentMessageIds.add(msgId);
        appendMessageToChat(text, "user", new Date().toISOString(), true, {
            user_id: window.userId,
            client_message_id: msgId,
        });
        fetch("/send-user-message", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                message: text,
                user_id: window.userId,
                client_message_id: msgId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                // Bisa tambahkan notifikasi atau tindakan lain jika perlu.
                chatInputElem.value = "";
            })
            .catch((error) => {
                console.error("Error sending user message:", error);
            });
    }
}

function loadTemplateTanyaJawab() {
    fetch("/template_tanya_jawab")
        .then((res) => res.json())
        .then((templates) => {
            const templateContainer =
                document.getElementById("templateTanyaJawab");
            templateContainer.innerHTML = "<h6>Pertanyaan Template:</h6>";
            templates.forEach((template) => {
                const btn = document.createElement("button");
                btn.classList.add("btn", "btn-outline-primary", "m-1");
                btn.textContent = template.pertanyaan;
                btn.addEventListener("click", function () {
                    const now = new Date().toISOString();
                    const clientMessageId =
                        Date.now() +
                        "_" +
                        Math.random().toString(36).substring(2, 10);

                    // Jika user anonim (ID default dimulai dengan "user_"),
                    // langsung tampilkan pesan template secara lokal dengan label "Anda"
                    if (window.userId.startsWith("user_")) {
                        appendMessageToChat(
                            template.pertanyaan,
                            "user",
                            now,
                            true,
                            {
                                user_id: window.userId,
                                user_name: "Anda",
                                template: true,
                                client_message_id: clientMessageId,
                            }
                        );
                        // Tampilkan jawaban admin setelah 1 detik
                        setTimeout(() => {
                            appendMessageToChat(
                                template.jawaban,
                                "admin",
                                new Date().toISOString(),
                                true,
                                { target: window.userId, template: true }
                            );
                        }, 1000);
                    } else {
                        // Untuk user yang terautentikasi, kirim pesan ke server.
                        // Jangan tampilkan pesan secara lokal agar tidak terjadi duplikasi
                        // (nanti pesan akan tampil melalui 'loadChatMessages')
                        fetch("/send-user-message", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                Accept: "application/json",
                            },
                            body: JSON.stringify({
                                message: template.pertanyaan,
                                user_id: window.userId,
                                client_message_id: clientMessageId,
                                // Jangan sertakan user_name agar backend (atau loadChatMessages)
                                // secara default menampilkan label "Anda"
                            }),
                        })
                            .then((res) => res.json())
                            .then((data) => {
                                // Opsional: bisa langsung refresh chat atau biarkan polling mengambil pesan terbaru
                            })
                            .catch((error) =>
                                console.error(
                                    "Error mengirim pertanyaan:",
                                    error
                                )
                            );

                        setTimeout(() => {
                            fetch("/send-admin-template", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": csrfToken,
                                    Accept: "application/json",
                                },
                                body: JSON.stringify({
                                    message: template.jawaban,
                                    target: window.userId,
                                }),
                            })
                                .then((res) => res.json())
                                .then((data) => {
                                    // Opsional: bisa lakukan sesuatu jika diperlukan
                                })
                                .catch((error) => {
                                    console.error(
                                        "Error mengirim jawaban:",
                                        error
                                    );
                                    // Jika terjadi error, fallback saja dengan menampilkan jawaban admin
                                    appendMessageToChat(
                                        template.jawaban,
                                        "admin",
                                        new Date().toISOString(),
                                        true,
                                        {
                                            target: window.userId,
                                            template: true,
                                        }
                                    );
                                });
                        }, 1000);
                    }
                });
                templateContainer.appendChild(btn);
            });
        })
        .catch((err) => console.error("Error fetching templates:", err));
}

// Jika chatMessagesDiv ada, tambahkan event listener scroll
if (chatMessagesDiv) {
    chatMessagesDiv.addEventListener(
        "scroll",
        debounce(function () {
            if (
                chatMessagesDiv.scrollTop + chatMessagesDiv.clientHeight >=
                chatMessagesDiv.scrollHeight - 50
            ) {
                // Panggil endpoint untuk menandai pesan sebagai read (untuk pesan admin)
                markMessageAsRead(window.userId, null);
            }
        }, 500)
    );
}

$("#chatModal").on("shown.bs.modal", () => {
    loadChatMessages();
    loadTemplateTanyaJawab();
    $('#chatInput').trigger('focus');
});

$("#chatModal").on("hidden.bs.modal", function () {
    $(this).find(":focus").blur();
});

let btnSendChat = document.getElementById("sendChat"),
    chatInput = document.getElementById("chatInput");
if (btnSendChat) {
    btnSendChat.addEventListener("click", sendUserMessage);
}
if (chatInput) {
    chatInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            sendUserMessage();
        }
    });
}
