// Jika belum ada userId, generate dan simpan di localStorage
let userId = localStorage.getItem("userId");
if (!userId) {
    userId = "user_" + Math.random().toString(36).substring(2, 10);
    localStorage.setItem("userId", userId);
}

// Fungsi untuk menyimpan pesan ke localStorage (termasuk user_id bila ada)
function saveMessage(data) {
    let messages = JSON.parse(localStorage.getItem("chatMessages")) || [];
    messages.push(data);
    localStorage.setItem("chatMessages", JSON.stringify(messages));
}

// Fungsi untuk menampilkan pesan ke chat
function appendMessageToChat(
    message,
    sender,
    timestamp = new Date().toISOString(),
    save = true,
    extra = {}
) {
    const chatMessagesEl = document.getElementById("chatMessages");
    const messageEl = document.createElement("div");
    messageEl.classList.add("d-flex", "mb-2");

    if (sender === "admin") {
        messageEl.innerHTML = `
            <span class="fw-bold me-2" style="min-width:70px">Admin</span>
            <div class="p-2 bg-light rounded" style="max-width:75%;">${message}</div>
        `;
    } else {
        // Untuk user, tampilkan di sebelah kanan
        messageEl.classList.add("flex-row-reverse");
        messageEl.innerHTML = `
            <span class="fw-bold ms-2" style="min-width:70px">Anda</span>
            <div class="p-2 bg-primary text-white rounded" style="max-width:75%;">${message}</div>
        `;
    }
    chatMessagesEl.appendChild(messageEl);

    if (save) {
        // Buat objek pesan baru
        const newMessage = {
            id: Date.now(),
            sender,
            message,
            timestamp,
        };

        // Sertakan properti tambahan jika ada (misalnya user_id, target, atau template)
        if (extra.user_id) newMessage.user_id = extra.user_id;
        if (extra.target) newMessage.target = extra.target;
        if (extra.template) newMessage.template = true;

        // Ambil pesan yang sudah tersimpan
        let messages = JSON.parse(localStorage.getItem("chatMessages")) || [];

        // Cek duplikasi secara konten jika pesan template (flag template true)
        if (newMessage.template) {
            const duplicate = messages.some((msg) => {
                return (
                    msg.sender === newMessage.sender &&
                    msg.message === newMessage.message &&
                    msg.template
                );
            });
            if (duplicate) {
                // Jika sudah ada pesan template dengan konten yang sama, jangan simpan lagi
                return;
            }
        } else {
            // Cek duplikasi berdasarkan sender, message, dan timestamp (toleransi 2 detik)
            const duplicate = messages.some((msg) => {
                return (
                    msg.sender === newMessage.sender &&
                    msg.message === newMessage.message &&
                    Math.abs(
                        new Date(msg.timestamp) - new Date(newMessage.timestamp)
                    ) < 2000
                );
            });
            if (duplicate) {
                return;
            }
        }

        messages.push(newMessage);
        localStorage.setItem("chatMessages", JSON.stringify(messages));
        // Jika diperlukan, update DataTable atau chat list
        // loadChatTable();
    }
}

function loadChatMessages() {
    document.getElementById("chatMessages").innerHTML = "";
    (JSON.parse(localStorage.getItem("chatMessages")) || []).forEach((msg) => {
        appendMessageToChat(msg.message, msg.sender, msg.timestamp, false);
    });
}

// Inisialisasi modal chat
$("#chatModal").on("shown.bs.modal", function () {
    loadChatMessages();
});

// Fungsi untuk mengirim pesan user
function sendUserMessage() {
    const chatInput = document.getElementById("chatInput");
    const text = chatInput.value.trim();
    if (text !== "") {
        fetch(
            "/send-user-message?message=" +
                encodeURIComponent(text) +
                "&user_id=" +
                encodeURIComponent(userId)
        )
            .then((response) => response.json())
            .then((data) => {
                console.log(data.message);
                chatInput.value = "";
            })
            .catch((error) => console.error("Error:", error));
    }
}

// Mengirim pesan user dengan tombol kirim
document.getElementById("sendChat").addEventListener("click", sendUserMessage);

// Mengirim pesan user saat menekan tombol Enter di input chat
document.getElementById("chatInput").addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault(); // mencegah newline jika diperlukan
        sendUserMessage();
    }
});

let lastTemplateQuestion = "";

// Fungsi untuk memuat template tanya jawab dari server dan menampilkannya di modal chat
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
                // Event ketika template diklik
                btn.addEventListener("click", function () {
                    const ts = new Date().toISOString();

                    // Tampilkan pesan pertanyaan secara manual dan simpan ke localStorage (save=true)
                    lastTemplateQuestion = template.pertanyaan;
                    appendMessageToChat(
                        template.pertanyaan,
                        "user",
                        ts,
                        true, // simpan ke localStorage
                        { user_id: userId, template: true }
                    );

                    // Kirim pesan pertanyaan ke server (untuk broadcast ke admin)
                    fetch(
                        "/send-user-message?message=" +
                            encodeURIComponent(template.pertanyaan) +
                            "&user_id=" +
                            encodeURIComponent(userId)
                    )
                        .then((response) => response.json())
                        .then((data) => {
                            console.log("Pertanyaan terkirim:", data.message);
                        })
                        .catch((error) =>
                            console.error("Error mengirim pertanyaan:", error)
                        );

                    // Tunda pengiriman pesan jawaban agar pertanyaan tampil dulu
                    setTimeout(() => {
                        fetch(
                            "/send-admin-message?message=" +
                                encodeURIComponent(template.jawaban) +
                                "&target=" +
                                encodeURIComponent(userId)
                        )
                            .then((response) => response.json())
                            .then((data) => {
                                console.log("Jawaban terkirim:", data.message);
                            })
                            .catch((error) => {
                                console.error("Error mengirim jawaban:", error);
                                // Jika terjadi error, sebagai fallback tampilkan jawaban secara manual
                                appendMessageToChat(
                                    template.jawaban,
                                    "admin",
                                    new Date().toISOString(),
                                    true,
                                    { target: userId, template: true }
                                );
                            });
                    }, 1000); // delay 1 detik
                });
                container.appendChild(btn);
            });
        })
        .catch((error) => console.error("Error fetching templates:", error));
}

// Panggil loadTemplateTanyaJawab ketika modal chat ditampilkan
$("#chatModal").on("shown.bs.modal", function () {
    loadChatMessages();
    loadTemplateTanyaJawab();
});
