if (typeof window.userId === "undefined") {
    let tempUser = localStorage.getItem("userId");
    if (!tempUser) {
        tempUser = "user_" + Math.random().toString(36).substring(2, 10);
        localStorage.setItem("userId", tempUser);
    }
    window.userId = tempUser;
}

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

/**
 * Simpan pesan ke localStorage.
 */
function saveMessage(message) {
    const messages = JSON.parse(localStorage.getItem("chatMessages")) || [];
    messages.push(message);
    localStorage.setItem("chatMessages", JSON.stringify(messages));
}

function markMessageAsRead(messageId, buttonElement) {
    fetch(`/chat/mark-read/${messageId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            console.log(data.message);
            // Ubah tampilan tombol menjadi label "Read"
            if (buttonElement) {
                buttonElement.textContent = "Read";
                buttonElement.style.pointerEvents = "none";
            }
        })
        .catch((err) => console.error("Error marking message as read:", err));
}

/**
 * Append pesan ke elemen DOM chat.
 */
function appendMessageToChat(
    messageContent,
    sender,
    timestamp = new Date().toISOString(),
    store = true,
    extra = {}
) {
    const chatContainer = document.getElementById("chatMessages");
    const messageElement = document.createElement("div");

    if (sender === "admin") {
        messageElement.classList.add("d-flex", "mb-2");
        messageElement.innerHTML = `
            <span class="fw-bold me-2" style="min-width:70px">Admin</span>
            <div class="p-2 bg-light rounded" style="max-width:75%;">${messageContent}</div>
        `;
    } else {
        messageElement.classList.add("d-flex", "mb-2", "flex-row-reverse");
        const displayName = extra.user_name || "Anda";
        messageElement.innerHTML = `
            <span class="fw-bold ms-2" style="min-width:70px">${displayName}</span>
            <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">${messageContent}</div>
        `;
    }
    chatContainer.appendChild(messageElement);
    chatContainer.scrollTop = chatContainer.scrollHeight;

    if (store) {
        const messageData = {
            message_id: extra.client_message_id || Date.now(),
            sender: sender,
            message: messageContent,
            timestamp: timestamp,
        };
        if (extra.user_id) messageData.user_id = extra.user_id;
        if (extra.target) messageData.target = extra.target;
        if (extra.user_name) messageData.user_name = extra.user_name;
        if (extra.template) messageData.template = true;

        const storedMessages =
            JSON.parse(localStorage.getItem("chatMessages")) || [];
        // Cegah duplikasi berdasarkan client_message_id atau pesan serupa dalam waktu singkat
        if (extra.client_message_id) {
            if (
                !storedMessages.some(
                    (m) => m.message_id === extra.client_message_id
                )
            ) {
                storedMessages.push(messageData);
            }
        } else {
            if (
                !storedMessages.some(
                    (m) =>
                        m.sender === sender &&
                        m.message === messageContent &&
                        Math.abs(new Date(m.timestamp) - new Date(timestamp)) <
                            2000
                )
            ) {
                storedMessages.push(messageData);
            }
        }
        localStorage.setItem("chatMessages", JSON.stringify(storedMessages));
    }
}

/**
 * Load pesan dari localStorage dan tampilkan ke UI.
 */
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
            // Kosongkan container pesan
            const container = document.getElementById("chatMessages");
            container.innerHTML = "";
            // Tampilkan masing-masing pesan
            messages.forEach((msg) => {
                // Gunakan msg.created_at sebagai timestamp. Jika diperlukan, konversi ke ISO string.
                // Gunakan msg.sender_type untuk menentukan tampilan pesan.
                const sender = msg.sender_type === "admin" ? "admin" : "user";
                const userName = sender === "admin" ? "Admin" : msg.user_name;
                appendMessageToChat(
                    msg.message,
                    sender,
                    msg.created_at,
                    false,
                    {
                        user_id: msg.user_id,
                        user_name: userName,
                        client_message_id: msg.client_message_id,
                    }
                );
            });
        })
        .catch((err) => {
            console.error("Error loading chat messages:", err);
        });
}

/**
 * Kirim pesan user ke server.
 */
function sendUserMessage() {
    const chatInput = document.getElementById("chatInput");
    const messageText = chatInput.value.trim();
    if (messageText !== "") {
        // Buat client_message_id unik
        const clientMessageId =
            Date.now() + "_" + Math.random().toString(36).substring(2, 10);
        window.sentMessageIds.add(clientMessageId);

        // Optimistic UI update
        appendMessageToChat(
            messageText,
            "user",
            new Date().toISOString(),
            true,
            {
                user_id: window.userId,
                client_message_id: clientMessageId,
            }
        );

        fetch("/send-user-message", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                message: messageText,
                user_id: window.userId,
                client_message_id: clientMessageId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log(data.message);
                chatInput.value = "";
            })
            .catch((error) => console.error("Error:", error));
    }
}

/**
 * Load template pertanyaan dan jawaban.
 */
function loadTemplateTanyaJawab() {
    fetch("/template_tanya_jawab")
        .then((response) => response.json())
        .then((data) => {
            const templateContainer =
                document.getElementById("templateTanyaJawab");
            templateContainer.innerHTML = "<h6>Pertanyaan Template:</h6>";
            data.forEach((item) => {
                const btn = document.createElement("button");
                btn.classList.add("btn", "btn-outline-primary", "m-1");
                btn.textContent = item.pertanyaan;
                btn.addEventListener("click", function () {
                    const timestamp = new Date().toISOString();
                    const clientMessageId =
                        Date.now() +
                        "_" +
                        Math.random().toString(36).substring(2, 10);
                    // Tampilkan pertanyaan template sebagai pesan user
                    appendMessageToChat(
                        item.pertanyaan,
                        "user",
                        timestamp,
                        true,
                        {
                            user_id: window.userId,
                            user_name: window.userName,
                            template: true,
                            client_message_id: clientMessageId,
                        }
                    );

                    // Jika user sudah login (misalnya userId bukan diawali "user_")
                    if (!window.userId.startsWith("user_")) {
                        // Kirim ke server: pertanyaan
                        fetch("/send-user-message", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                                Accept: "application/json",
                            },
                            body: JSON.stringify({
                                message: item.pertanyaan,
                                user_id: window.userId,
                                client_message_id: clientMessageId,
                            }),
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                console.log(
                                    "Pertanyaan terkirim:",
                                    data.message
                                );
                            })
                            .catch((err) =>
                                console.error("Error mengirim pertanyaan:", err)
                            );

                        // Setelah delay, kirim jawaban admin melalui server
                        setTimeout(() => {
                            fetch("/send-admin-template", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute("content"),
                                    Accept: "application/json",
                                },
                                body: JSON.stringify({
                                    message: item.jawaban,
                                    target: window.userId,
                                }),
                            })
                                .then((response) => response.json())
                                .then((data) => {
                                    console.log(
                                        "Jawaban terkirim:",
                                        data.message
                                    );
                                })
                                .catch((err) => {
                                    console.error(
                                        "Error mengirim jawaban:",
                                        err
                                    );
                                    appendMessageToChat(
                                        item.jawaban,
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
                    } else {
                        // Jika user belum login, tampilkan jawaban admin secara lokal
                        setTimeout(() => {
                            appendMessageToChat(
                                item.jawaban,
                                "admin",
                                new Date().toISOString(),
                                true,
                                {
                                    target: window.userId,
                                    template: true,
                                }
                            );
                        }, 1000);
                    }
                });
                templateContainer.appendChild(btn);
            });
        })
        .catch((err) => console.error("Error fetching templates:", err));
}

// Ketika modal chat terbuka, load pesan dan template
$("#chatModal").on("shown.bs.modal", () => {
    loadChatMessages();
    loadTemplateTanyaJawab();
});

$("#chatModal").on("hidden.bs.modal", function () {
    // Hapus fokus dari elemen yang masih aktif di dalam modal
    $(this).find(":focus").blur();
});

// Event listener untuk tombol kirim dan input enter
document.getElementById("sendChat").addEventListener("click", sendUserMessage);
document.getElementById("chatInput").addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        e.preventDefault();
        sendUserMessage();
    }
});
