// assets/dashboard/js/chat-user.js (Versi Real-time, Debounce diperbaiki)

document.addEventListener("DOMContentLoaded", () => {
    const chatModalElement = document.getElementById("chatModal");
    const chatMessagesContainer = document.getElementById("chatMessages");
    const templateContainer = document.getElementById("templateTanyaJawab");
    const chatMessageForm = document.getElementById("chatMessageForm");
    const chatInput = document.getElementById("chatInput");
    const chatLoadingIndicator = document.getElementById(
        "chatLoadingIndicator"
    );
    const templateLoadingIndicator = document.getElementById(
        "templateLoadingIndicator"
    );
    const chatBadge = document.getElementById("chatIconBadge"); // Asumsi ID badge

    if (!chatModalElement) {
        console.warn(
            "Chat modal element (#chatModal) not found. User chat disabled."
        );
        return;
    }

    const currentUserId = chatModalElement.dataset.userId || null;
    const isLoggedIn = !!currentUserId;
    const apiClient = window.axios; // Prioritaskan Axios

    let currentPage = 1;
    let isLoadingHistory = false;
    let hasMoreHistory = true;
    let userEchoChannel = null;
    let unreadCount = 0;
    let markAsReadTimer = null; // Timer untuk debounce mark as read

    // --- Helper Functions ---
    function escapeHTML(str) {
        if (str === null || str === undefined) return "";
        str = String(str);
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatTimestamp(isoString) {
        if (!isoString) return "";
        try {
            const date = new Date(isoString);
            return date.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            });
        } catch (e) {
            console.error(
                "Error formatting timestamp:",
                e,
                "Input:",
                isoString
            );
            return "";
        }
    }

    // === FIX: Tambahkan kembali fungsi debounce ===
    function debounce(func, wait) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), wait);
        };
    }
    // ==========================================

    // --- UI Update Functions ---
    function updateUnreadBadge() {
        if (chatBadge) {
            if (unreadCount > 0) {
                chatBadge.textContent = unreadCount > 9 ? "9+" : unreadCount;
                chatBadge.style.display = "flex";
            } else {
                chatBadge.style.display = "none";
            }
        }
    }

    function scrollChatToBottom(force = false) {
        if (chatMessagesContainer) {
            const container = chatMessagesContainer;
            const threshold = 150;
            const shouldScroll =
                force ||
                container.scrollHeight -
                    container.scrollTop -
                    container.clientHeight <
                    threshold;
            if (shouldScroll) {
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 50);
            }
        }
    }

    function displayChatMessage(messageData, prepend = false) {
        if (!chatMessagesContainer || !messageData || !messageData.sender_type)
            return;
        if (chatLoadingIndicator) chatLoadingIndicator.style.display = "none";
        const placeholder = chatMessagesContainer.querySelector(
            ".no-messages-placeholder"
        );
        if (placeholder) placeholder.remove();

        const msgDiv = document.createElement("div");
        msgDiv.classList.add("mb-2", "d-flex");
        msgDiv.dataset.messageId = messageData.id || `optimistic_${Date.now()}`;

        const isSentByUser = messageData.sender_type === "user";
        msgDiv.classList.add(
            isSentByUser ? "justify-content-end" : "justify-content-start"
        );

        const bubbleDiv = document.createElement("div");
        bubbleDiv.classList.add("p-2", "rounded", "mw-75", "chat-bubble");
        bubbleDiv.style.wordWrap = "break-word";
        bubbleDiv.style.maxWidth = "75%";
        bubbleDiv.classList.add(
            isSentByUser ? "bg-primary" : "bg-secondary",
            "text-white"
        );

        if (!isSentByUser && messageData.sender_name) {
            const senderNameSpan = document.createElement("small");
            senderNameSpan.classList.add(
                "d-block",
                "fw-bold",
                "mb-1",
                "sender-name"
            );
            senderNameSpan.textContent = escapeHTML(messageData.sender_name);
            bubbleDiv.appendChild(senderNameSpan);
        }

        const messageText = document.createElement("div");
        messageText.classList.add("message-body");
        messageText.textContent = messageData.message;
        bubbleDiv.appendChild(messageText);

        const timeSpan = document.createElement("small");
        timeSpan.classList.add(
            "d-block",
            "mt-1",
            "text-end",
            "opacity-75",
            "message-timestamp"
        );
        timeSpan.textContent = formatTimestamp(messageData.created_at) + " ";

        if (isSentByUser) {
            const readIndicator = document.createElement("i");
            readIndicator.classList.add("bi", "read-indicator");
            if (messageData.read_at) {
                readIndicator.classList.add("bi-check2-all", "text-info");
            } else if (
                !String(msgDiv.dataset.messageId).startsWith("optimistic_")
            ) {
                readIndicator.classList.add("bi-check2");
            } else {
                readIndicator.classList.add("bi-clock", "text-warning");
            }
            timeSpan.appendChild(readIndicator);
        }
        bubbleDiv.appendChild(timeSpan);
        msgDiv.appendChild(bubbleDiv);

        if (prepend) {
            chatMessagesContainer.insertBefore(
                msgDiv,
                chatMessagesContainer.firstChild
            );
        } else {
            chatMessagesContainer.appendChild(msgDiv);
        }
    }

    function displayTemplateButtons(templates) {
        if (!templateContainer || !templates) return;
        if (templateLoadingIndicator) templateLoadingIndicator.remove();
        templateContainer.innerHTML =
            '<small class="text-muted d-block mb-1">Pilih pertanyaan cepat:</small>';
        const buttonWrapper = document.createElement("div");
        buttonWrapper.classList.add("d-flex", "flex-wrap", "gap-1");

        templates.forEach((template) => {
            const button = document.createElement("button");
            button.type = "button";
            button.classList.add("btn", "btn-sm", "btn-outline-primary");
            button.textContent = escapeHTML(template.pertanyaan);
            button.onclick = () => handleTemplateClick(template);
            buttonWrapper.appendChild(button);
        });
        templateContainer.appendChild(buttonWrapper);
    }

    function handleTemplateClick(template) {
        const userQuestion = template.pertanyaan;
        const adminAnswer = template.jawaban;

        displayChatMessage({
            id: `temp_q_${Date.now()}`,
            sender_type: "user",
            message: userQuestion,
            created_at: new Date().toISOString(),
        });
        scrollChatToBottom(true);

        if (isLoggedIn) {
            sendUserMessageToServer(userQuestion);
        } else {
            setTimeout(() => {
                displayChatMessage({
                    id: `temp_a_${Date.now()}`,
                    sender_type: "admin",
                    sender_name: "Admin Gereja",
                    message: adminAnswer,
                    created_at: new Date().toISOString(),
                });
                scrollChatToBottom(true);
            }, 750);
        }
    }

    // --- API Functions ---
    async function fetchHistory(page = 1) {
        if (!isLoggedIn || isLoadingHistory) return;
        isLoadingHistory = true;
        if (page === 1 && chatLoadingIndicator)
            chatLoadingIndicator.style.display = "flex";

        if (!apiClient) {
            console.error("[ChatUser] Axios is not available.");
            if (page === 1 && chatMessagesContainer)
                chatMessagesContainer.innerHTML =
                    '<div class="text-center text-danger p-3">Gagal memuat: API Client tidak ada.</div>';
            if (chatLoadingIndicator)
                chatLoadingIndicator.style.display = "none";
            isLoadingHistory = false;
            return;
        }

        try {
            const response = await apiClient.get(
                `/chat/my-history?page=${page}`
            );
            const paginatedData = response.data;

            if (chatLoadingIndicator && page === 1)
                chatLoadingIndicator.style.display = "none";

            const messages = paginatedData.data;
            const initialScrollHeight = chatMessagesContainer.scrollHeight;

            if (messages.length === 0 && page === 1) {
                chatMessagesContainer.innerHTML =
                    '<div class="text-center text-muted p-3 no-messages-placeholder">Belum ada pesan. Mulai percakapan!</div>';
            } else {
                messages
                    .reverse()
                    .forEach((message) => displayChatMessage(message, true));
            }

            if (page === 1) {
                markAdminMessagesAsRead();
                scrollChatToBottom(true);
            } else if (messages.length > 0) {
                chatMessagesContainer.scrollTop =
                    chatMessagesContainer.scrollHeight - initialScrollHeight;
            }

            hasMoreHistory = !!paginatedData.links.next;
            currentPage = paginatedData.meta.current_page;
        } catch (error) {
            console.error(
                "[ChatUser] Error fetching history:",
                error.response?.data || error.message || error
            );
            if (page === 1 && chatMessagesContainer) {
                if (chatLoadingIndicator)
                    chatLoadingIndicator.style.display = "none";
                chatMessagesContainer.innerHTML =
                    '<div class="text-center text-danger p-3">Gagal memuat riwayat chat.</div>';
            }
        } finally {
            isLoadingHistory = false;
        }
    }

    async function fetchTemplates() {
        if (templateLoadingIndicator)
            templateLoadingIndicator.style.display = "block";
        if (!apiClient) {
            console.error("[ChatUser] Axios is not available for templates.");
            if (templateContainer)
                templateContainer.innerHTML =
                    '<small class="text-danger">Gagal memuat template: API Client tidak ada.</small>';
            if (templateLoadingIndicator)
                templateLoadingIndicator.style.display = "none";
            return;
        }
        try {
            const response = await apiClient.get("/chat-templates");
            displayTemplateButtons(response.data);
        } catch (error) {
            console.error(
                "[ChatUser] Error fetching templates:",
                error.response?.data || error.message || error
            );
            if (templateContainer)
                templateContainer.innerHTML =
                    '<small class="text-danger">Gagal memuat template.</small>';
        } finally {
            if (templateLoadingIndicator)
                templateLoadingIndicator.style.display = "none";
        }
    }

    async function sendUserMessageToServer(messageText) {
        if (!isLoggedIn || !messageText || !apiClient) return;

        const optimisticId = `optimistic_${Date.now()}`;
        // Optimistic UI sudah ditangani di event submit / template click

        try {
            const response = await apiClient.post("/chat/send-user", {
                message: messageText,
            });
            const serverMessageData = response.data.data;

            // Update ID dan status pesan optimistic
            const $optimisticMessage = $(`[data-message-id^="optimistic_"]`)
                .filter(function () {
                    return $(this).find(".message-body").text() === messageText;
                })
                .last(); // Gunakan jQuery jika tersedia untuk kemudahan seleksi

            if ($optimisticMessage.length && serverMessageData) {
                $optimisticMessage.attr(
                    "data-message-id",
                    serverMessageData.id
                );
                $optimisticMessage
                    .find(".read-indicator")
                    .removeClass("bi-clock text-warning")
                    .addClass("bi-check2");
            } else {
                console.warn(
                    "[ChatUser] Could not find optimistic message to update or server data missing."
                );
            }
        } catch (error) {
            console.error(
                "[ChatUser] Error sending message:",
                error.response?.data || error.message || error
            );
            $(`[data-message-id^="optimistic_"]`)
                .filter(function () {
                    return $(this).find(".message-body").text() === messageText;
                })
                .remove();
            alert("Gagal mengirim pesan. Silakan coba lagi.");
        }
    }

    async function markAdminMessagesAsRead() {
        if (!isLoggedIn || !apiClient) return;
        clearTimeout(markAsReadTimer); // Debounce
        markAsReadTimer = setTimeout(async () => {
            try {
                await apiClient.post("/chat/mark-read");
            } catch (error) {
                console.error(
                    "[ChatUser] Error marking admin messages as read:",
                    error.response?.data || error.message || error
                );
            }
        }, 500); // Delay sedikit
    }

    // --- Echo Listener Function ---
    function initializeUserEcho() {
        if (!isLoggedIn || typeof window.Echo === "undefined") {
            return;
        }
        if (userEchoChannel) {
            return;
        }
        const channelName = `private-chat.user.${currentUserId}`;

        try {
            userEchoChannel = window.Echo.private(channelName);
            userEchoChannel
                .subscribed(() => {
                    console.log(`[Echo] Subscribed to ${channelName}`);
                })
                .listen(".message.sent", (event) => {
                    if (
                        event.sender_type === "user" &&
                        event.sender_id == currentUserId
                    ) {
                        const $optimisticMsg = $(
                            `[data-message-id^="optimistic_"]`
                        )
                            .filter(function () {
                                return (
                                    $(this).find(".message-body").text() ===
                                    event.message
                                );
                            })
                            .last();
                        if ($optimisticMsg.length) {
                            $optimisticMsg.attr("data-message-id", event.id);
                            $optimisticMsg
                                .find(".read-indicator")
                                .removeClass("bi-clock text-warning")
                                .addClass("bi-check2");
                        }
                        return;
                    }

                    displayChatMessage(event);
                    const isModalOpen =
                        chatModalElement.classList.contains("show");

                    if (isModalOpen) {
                        scrollChatToBottom(true);
                        if (event.sender_type === "admin")
                            markAdminMessagesAsRead();
                    } else {
                        unreadCount++;
                        updateUnreadBadge();
                        if (event.sender_type === "admin") {
                            showBrowserNotification(
                                `Pesan baru dari ${
                                    escapeHTML(event.sender_name) || "Admin"
                                }`,
                                event.message
                            );
                        }
                    }
                })
                .listen(".messages.read", (event) => {
                    if (event.readerType === "admin") {
                        updateUserMessagesReadStatus(event.lastReadMessageId);
                    }
                })
                .error((error) => {
                    console.error(
                        `[Echo] Subscription FAILED for ${channelName}`,
                        JSON.stringify(error)
                    );
                    userEchoChannel = null;
                });
        } catch (e) {
            console.error("[ChatUser] Error initializing Echo:", e);
        }
    }

    function updateUserMessagesReadStatus(lastReadMessageId) {
        if (!chatMessagesContainer) return;
        chatMessagesContainer
            .querySelectorAll(".d-flex.justify-content-end .chat-bubble")
            .forEach((bubble) => {
                const msgDiv = bubble.closest("[data-message-id]");
                if (!msgDiv) return;
                const messageId = parseInt(msgDiv.dataset.messageId, 10);
                const indicator = bubble.querySelector(".read-indicator");

                if (
                    indicator &&
                    !indicator.classList.contains("text-info") &&
                    !indicator.classList.contains("bi-clock")
                ) {
                    if (
                        lastReadMessageId === null ||
                        (messageId && messageId <= lastReadMessageId)
                    ) {
                        indicator.classList.remove("bi-check2");
                        indicator.classList.add("bi-check2-all", "text-info");
                    }
                }
            });
    }

    function showBrowserNotification(title, body) {
        if (!("Notification" in window)) return;
        if (Notification.permission === "granted") {
            new Notification(title, { body: body, icon: "/img/logo-gpib.png" }); // Ganti path icon jika perlu
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then((permission) => {
                if (permission === "granted") {
                    new Notification(title, {
                        body: body,
                        icon: "/img/logo-gpib.png",
                    });
                }
            });
        }
    }

    // --- Event Listeners ---
    if (chatMessageForm && isLoggedIn) {
        chatMessageForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const messageText = chatInput.value.trim();
            if (messageText) {
                displayChatMessage({
                    id: `optimistic_${Date.now()}`,
                    sender_type: "user",
                    message: messageText,
                    created_at: new Date().toISOString(),
                    read_at: null,
                });
                scrollChatToBottom(true);
                sendUserMessageToServer(messageText);
                chatInput.value = "";
            }
        });
    }

    chatModalElement.addEventListener("shown.bs.modal", () => {
        unreadCount = 0;
        updateUnreadBadge();
        if (isLoggedIn) {
            currentPage = 1;
            hasMoreHistory = true;
            if (chatMessagesContainer) chatMessagesContainer.innerHTML = "";
            if (chatLoadingIndicator)
                chatLoadingIndicator.style.display = "flex";
            fetchHistory(1);
        } else {
            if (chatMessagesContainer) chatMessagesContainer.innerHTML = "";
            if (chatLoadingIndicator)
                chatLoadingIndicator.style.display = "none";
        }
        fetchTemplates();
        if (chatInput) chatInput.focus();
    });

    chatModalElement.addEventListener("hidden.bs.modal", () => {
        // Tidak leave channel Echo
        const focusedElement = chatModalElement.querySelector(":focus");
        if (focusedElement) focusedElement.blur();
    });

    if (chatMessagesContainer && isLoggedIn) {
        // Gunakan fungsi debounce yang sudah didefinisikan
        chatMessagesContainer.addEventListener(
            "scroll",
            debounce(() => {
                if (
                    chatMessagesContainer.scrollTop === 0 &&
                    hasMoreHistory &&
                    !isLoadingHistory
                ) {
                    fetchHistory(currentPage + 1);
                }
            }, 300)
        ); // Panggil debounce di sini
    }

    // --- Inisialisasi Awal ---
    initializeUserEcho(); // Inisialisasi Echo di awal jika user login
}); // Akhir DOMContentLoaded
