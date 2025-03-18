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
    messageText,
    sender,
    timestamp = new Date().toISOString(),
    store = true,
    options = {}
) {
    const chatContainer = document.getElementById("chatMessages");
    let messageDiv = document.createElement("div");
    let readLabelHTML = "";
    if (options.read_at) {
        readLabelHTML =
            ' <span class="badge bg-success ms-2 read-label">Dilihat</span>';
    }

    if (sender === "admin") {
        // Pesan dari admin; tampil tanpa badge "Dilihat" karena read receipt untuk pesan masuk tidak perlu ditampilkan.
        messageDiv.classList.add("d-flex", "mb-2", "admin-message");
        messageDiv.innerHTML = `
        <span class="fw-bold me-2" style="min-width:70px">Admin</span>
        <div class="p-2 bg-light rounded" style="max-width:75%;">
            ${messageText}
            <br>
            <small>${getRelativeTime(timestamp)}</small>
        </div>
      `;
    } else {
        // Pesan dari user (outgoing)
        messageDiv.classList.add(
            "d-flex",
            "mb-2",
            "flex-row-reverse",
            "user-message"
        );
        messageDiv.innerHTML = `
        <span class="fw-bold ms-2" style="min-width:70px">${
            options.user_name || "Anda"
        }</span>
        <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">
            ${messageText}
            <br>
            <small>${getRelativeTime(timestamp)}${readLabelHTML}</small>
        </div>
      `;
    }

    chatContainer.appendChild(messageDiv);
    chatContainer.scrollTop = chatContainer.scrollHeight;

    if (store) {
        const msgObj = {
            message_id: options.client_message_id || Date.now(),
            sender: sender,
            message: messageText,
            timestamp: timestamp,
            ...options,
        };
        let storedMessages =
            JSON.parse(localStorage.getItem("chatMessages")) || [];
        const duplicate = options.client_message_id
            ? storedMessages.some(
                  (item) => item.message_id === options.client_message_id
              )
            : storedMessages.some(
                  (item) =>
                      item.sender === sender &&
                      item.message === messageText &&
                      Math.abs(new Date(item.timestamp) - new Date(timestamp)) <
                          2000
              );
        if (!duplicate) {
            storedMessages.push(msgObj);
            localStorage.setItem(
                "chatMessages",
                JSON.stringify(storedMessages)
            );
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
            document.getElementById("chatMessages").innerHTML = "";
            messages.forEach((message) => {
                const msgSender =
                    message.sender_type === "admin" ? "admin" : "user";
                const displayName =
                    msgSender === "admin" ? "Admin" : message.user_name;
                appendMessageToChat(
                    message.message,
                    msgSender,
                    message.created_at,
                    false,
                    {
                        user_id: message.user_id,
                        user_name: displayName,
                        client_message_id: message.client_message_id,
                        read_at: message.read_at,
                    }
                );
            });
        })
        .catch((error) => {
            console.error("Error loading chat messages:", error);
        });
}

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
        .then((response) => response.json())
        .then((templates) => {
            const container = document.getElementById("templateTanyaJawab");
            container.innerHTML = "<h6>Pertanyaan Template:</h6>";
            templates.forEach((template) => {
                const btn = document.createElement("button");
                btn.classList.add("btn", "btn-outline-primary", "m-1");
                btn.textContent = template.pertanyaan;
                btn.addEventListener("click", function () {
                    const now = new Date().toISOString();
                    const msgId =
                        Date.now() +
                        "_" +
                        Math.random().toString(36).substring(2, 10);
                    appendMessageToChat(
                        template.pertanyaan,
                        "user",
                        now,
                        true,
                        {
                            user_id: window.userId,
                            user_name: window.userName,
                            template: true,
                            client_message_id: msgId,
                        }
                    );
                    if (window.userId.startsWith("user_")) {
                        setTimeout(() => {
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
                        }, 1000);
                    } else {
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
                                client_message_id: msgId,
                            }),
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                // Tindakan tambahan jika diperlukan
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
                                .then((response) => response.json())
                                .then((data) => {
                                    // Tindakan tambahan jika diperlukan
                                })
                                .catch((error) => {
                                    console.error(
                                        "Error mengirim jawaban:",
                                        error
                                    );
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
                container.appendChild(btn);
            });
        })
        .catch((error) => console.error("Error fetching templates:", error));
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
