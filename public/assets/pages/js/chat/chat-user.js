document.addEventListener("DOMContentLoaded", () => {
    // --- Element References ---
    const chatModalElement = document.getElementById("chatModal");
    const chatMessagesElement = document.getElementById("chatMessages");
    const templateContainer = document.getElementById("templateTanyaJawab");
    const chatMessageForm = document.getElementById("chatMessageForm");
    const chatInputElement = document.getElementById("chatInput");
    const chatLoadingIndicator = document.getElementById(
        "chatLoadingIndicator"
    );
    const templateLoadingIndicator = document.getElementById(
        "templateLoadingIndicator"
    );
    const chatIconBadge = document.getElementById("chatIconBadge");

    if (!chatModalElement) return;

    // --- State Variables ---
    const currentUserId = chatModalElement.dataset.userId || null;
    const isLoggedIn = !!currentUserId;
    const apiClient = window.axios;
    let currentPage = 1;
    let isLoadingHistory = false;
    let hasMorePages = true;
    let echoChannel = null;
    let unreadMessagesCount = 0;
    let markAsReadTimeout = null;

    // --- Helper Functions ---
    function sanitizeHTML(str) {
        if (str === null || str === undefined) return "";
        const map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };
        return String(str).replace(/[&<>"']/g, (m) => map[m]);
    }

    function formatTimestamp(isoString) {
        if (!isoString) return "";
        try {
            return new Date(isoString).toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            });
        } catch (error) {
            console.error("Timestamp Format Error:", error);
            return "";
        }
    }

    function updateChatIconBadge() {
        if (!chatIconBadge) return;
        chatIconBadge.style.display = unreadMessagesCount > 0 ? "flex" : "none";
        if (unreadMessagesCount > 0) {
            chatIconBadge.textContent =
                unreadMessagesCount > 9 ? "9+" : unreadMessagesCount;
        }
    }

    function scrollToBottom(force = false) {
        if (!chatMessagesElement) return;
        const scrollThreshold = 150;
        const isNearBottom =
            chatMessagesElement.scrollHeight -
                chatMessagesElement.scrollTop -
                chatMessagesElement.clientHeight <
            scrollThreshold;
        if (force || isNearBottom) {
            setTimeout(() => {
                chatMessagesElement.scrollTop =
                    chatMessagesElement.scrollHeight;
            }, 50);
        }
    }

    function displayMessage(message, prepend = false) {
        if (!chatMessagesElement || !message || !message.sender_type) return;
        if (chatLoadingIndicator) chatLoadingIndicator.style.display = "none";
        chatMessagesElement.querySelector(".no-messages-placeholder")?.remove();

        const messageIdValue =
            message.id ??
            `optimistic_${Date.now()}_${Math.random()
                .toString(36)
                .substring(2, 9)}`;
        const messageIdStr = String(messageIdValue);

        if (
            !messageIdStr.startsWith("optimistic_") &&
            chatMessagesElement.querySelector(
                `[data-message-id="${messageIdStr}"]`
            )
        )
            return;

        const messageWrapper = document.createElement("div");
        messageWrapper.classList.add("mb-2", "d-flex");
        messageWrapper.dataset.messageId = messageIdStr;
        const isUserSender = message.sender_type === "user";
        messageWrapper.classList.add(
            isUserSender ? "justify-content-end" : "justify-content-start"
        );

        const bubble = document.createElement("div");
        bubble.classList.add(
            "p-2",
            "rounded",
            "mw-75",
            "chat-bubble",
            isUserSender ? "bg-primary" : "bg-secondary",
            "text-white"
        );
        bubble.style.wordWrap = "break-word";
        bubble.style.maxWidth = "75%";

        if (!isUserSender && message.sender_name) {
            const senderNameElement = document.createElement("small");
            senderNameElement.classList.add(
                "d-block",
                "fw-bold",
                "mb-1",
                "sender-name"
            );
            senderNameElement.textContent = sanitizeHTML(message.sender_name);
            bubble.appendChild(senderNameElement);
        }

        const messageBody = document.createElement("div");
        messageBody.classList.add("message-body");
        messageBody.textContent = message.message;
        bubble.appendChild(messageBody);

        const timestampElement = document.createElement("small");
        timestampElement.classList.add(
            "d-block",
            "mt-1",
            "text-end",
            "opacity-75",
            "message-timestamp"
        );
        timestampElement.textContent =
            formatTimestamp(message.created_at) + " ";

        if (isUserSender) {
            const readIndicator = document.createElement("i");
            readIndicator.classList.add("fas", "read-indicator");
            if (message.read_at) {
                readIndicator.classList.add("fa-check-double", "text-info");
            } else if (messageIdStr.startsWith("optimistic_")) {
                readIndicator.classList.add("fa-clock", "text-warning");
            } else {
                readIndicator.classList.add("fa-check");
            }
            timestampElement.appendChild(readIndicator);
        }
        bubble.appendChild(timestampElement);
        messageWrapper.appendChild(bubble);

        try {
            if (prepend)
                chatMessagesElement.insertBefore(
                    messageWrapper,
                    chatMessagesElement.firstChild
                );
            else chatMessagesElement.appendChild(messageWrapper);
        } catch (error) {
            console.error("DOM Append Error:", error);
        }
    }

    async function fetchHistory(page = 1) {
        if (!isLoggedIn || isLoadingHistory) return;
        isLoadingHistory = true;
        if (page === 1 && chatLoadingIndicator)
            chatLoadingIndicator.style.display = "flex";
        if (!apiClient) {
            isLoadingHistory = false;
            return;
        }
        try {
            const response = await apiClient.get(
                `/chat/my-history?page=${page}`
            );
            const historyData = response.data;
            if (page === 1 && chatLoadingIndicator)
                chatLoadingIndicator.style.display = "none";
            const messages = historyData.data;
            const currentScrollHeight = chatMessagesElement.scrollHeight;

            if (messages.length === 0 && page === 1) {
                chatMessagesElement.innerHTML =
                    '<div class="text-center text-muted p-3 no-messages-placeholder">Belum ada pesan.</div>';
            } else {
                messages.forEach((msg) => displayMessage(msg, true));
            }
            if (page === 1) {
                markAdminMessagesAsRead();
                scrollToBottom(true);
            } else if (messages.length > 0) {
                chatMessagesElement.scrollTop =
                    chatMessagesElement.scrollHeight - currentScrollHeight;
            }
            hasMorePages = !!historyData.links.next;
            currentPage = historyData.meta.current_page;
        } catch (error) {
            console.error(
                "Fetch History Error:",
                error.response?.data || error.message || error
            );
            if (page === 1 && chatMessagesElement) {
                if (chatLoadingIndicator)
                    chatLoadingIndicator.style.display = "none";
                chatMessagesElement.innerHTML =
                    '<div class="text-center text-danger p-3">Gagal memuat riwayat chat.</div>';
            }
        } finally {
            isLoadingHistory = false;
        }
    }

    async function markAdminMessagesAsRead() {
        if (!isLoggedIn || !apiClient) return;
        clearTimeout(markAsReadTimeout);
        markAsReadTimeout = setTimeout(async () => {
            try {
                await apiClient.post("/chat/mark-read");
            } catch (error) {
                console.error(
                    "Mark Read Error:",
                    error.response?.data || error.message
                );
            }
        }, 750);
    }

    async function fetchAndDisplayTemplates() {
        if (!templateContainer) return;
        if (templateLoadingIndicator)
            templateLoadingIndicator.style.display = "block";
        templateContainer.innerHTML = "";
        templateContainer.appendChild(templateLoadingIndicator);
        if (!apiClient) {
            templateContainer.innerHTML =
                '<small class="text-danger">Gagal: API Client tidak ada.</small>';
            return;
        }
        try {
            const response = await apiClient.get("/chat-templates");
            displayTemplates(response.data);
        } catch (error) {
            console.error(
                "Fetch Templates Error:",
                error.response?.data || error.message || error
            );
            templateContainer.innerHTML =
                '<small class="text-danger">Gagal memuat template.</small>';
        } finally {
            if (templateLoadingIndicator)
                templateLoadingIndicator.style.display = "none";
        }
    }

    function displayTemplates(templates) {
        if (!templateContainer) return;
        templateContainer.querySelector("#templateLoadingIndicator")?.remove();
        templateContainer.innerHTML = "";
        if (!templates || templates.length === 0) {
            templateContainer.innerHTML =
                '<small class="text-muted">Tidak ada pertanyaan cepat tersedia.</small>';
            return;
        }
        templateContainer.innerHTML =
            '<small class="text-muted d-block mb-1">Pilih pertanyaan cepat:</small>';
        const buttonWrapper = document.createElement("div");
        buttonWrapper.classList.add("d-flex", "flex-wrap", "gap-1");
        templates.forEach((template) => {
            if (!template.pertanyaan || !template.jawaban) return;
            const button = document.createElement("button");
            button.type = "button";
            button.classList.add("btn", "btn-sm", "btn-outline-primary");
            button.textContent = sanitizeHTML(template.pertanyaan);
            button.onclick = () => {
                if (!chatMessagesElement) return;
                displayMessage(
                    {
                        id: `temp_q_${Date.now()}`,
                        sender_type: "user",
                        message: template.pertanyaan,
                        created_at: new Date().toISOString(),
                    },
                    false
                );
                scrollToBottom(true);
                setTimeout(() => {
                    displayMessage(
                        {
                            id: `temp_a_${Date.now()}`,
                            sender_type: "admin",
                            sender_name: "Admin Gereja",
                            message: template.jawaban,
                            created_at: new Date().toISOString(),
                        },
                        false
                    );
                    scrollToBottom(true);
                }, 750);
            };
            buttonWrapper.appendChild(button);
        });
        templateContainer.appendChild(buttonWrapper);
    }

    async function sendMessage(messageText) {
        if (!isLoggedIn || !messageText || !apiClient) return;
        const optimisticId = `optimistic_${Date.now()}_${Math.random()
            .toString(36)
            .substring(2, 9)}`;
        displayMessage(
            {
                id: optimisticId,
                sender_type: "user",
                message: messageText,
                created_at: new Date().toISOString(),
                read_at: null,
            },
            false
        );
        scrollToBottom(true);
        if (chatInputElement) chatInputElement.value = "";

        try {
            const response = await apiClient.post("/chat/send-user", {
                message: messageText,
            });
            const serverMessageData = response.data.data;
            const optimisticMessageElement = chatMessagesElement?.querySelector(
                `[data-message-id="${optimisticId}"]`
            );
            if (optimisticMessageElement && serverMessageData) {
                optimisticMessageElement.dataset.messageId = String(
                    serverMessageData.id
                );
                const timestampEl =
                    optimisticMessageElement.querySelector(
                        ".message-timestamp"
                    );
                const indicator =
                    optimisticMessageElement.querySelector(".read-indicator");
                if (timestampEl && indicator) {
                    timestampEl.textContent =
                        formatTimestamp(serverMessageData.created_at) + " ";
                    indicator.classList.remove("fa-clock", "text-warning");
                    indicator.classList.add("fa-check");
                    timestampEl.appendChild(indicator);
                }
            } else if (!optimisticMessageElement && serverMessageData) {
                displayMessage(serverMessageData, false);
            }
        } catch (error) {
            console.error(
                "Send Message Error:",
                error.response?.data || error.message || error
            );
            const failedMessageElement = chatMessagesElement?.querySelector(
                `[data-message-id="${optimisticId}"]`
            );
            if (failedMessageElement) {
                failedMessageElement.classList.add("message-failed");
                const errorIndicator = document.createElement("i");
                errorIndicator.classList.add(
                    "fas",
                    "fa-exclamation-circle",
                    "text-danger",
                    "ms-1"
                );
                failedMessageElement.querySelector(".read-indicator")?.remove();
                failedMessageElement
                    .querySelector(".message-timestamp")
                    ?.appendChild(errorIndicator);
            }
            if (chatInputElement) chatInputElement.value = messageText;
        }
    }

    function setupEchoListener() {
        if (!isLoggedIn || typeof window.Echo === "undefined") return;
        if (echoChannel) return;
        const channelName = `private-chat.user.${currentUserId}`;
        try {
            echoChannel = window.Echo.private(channelName);
            echoChannel
                .subscribed(() => {
                    console.log(`[Echo] User subscribed to ${channelName}`);
                })
                .listen(".message.sent", (eventData) => {
                    if (
                        eventData.sender_type === "user" &&
                        eventData.sender_id == currentUserId
                    )
                        return;
                    if (eventData.sender_type === "admin") {
                        displayMessage(eventData, false);
                        if (chatModalElement?.classList.contains("show")) {
                            scrollToBottom(true);
                            markAdminMessagesAsRead();
                        } else {
                            unreadMessagesCount++;
                            updateChatIconBadge();
                            showDesktopNotification(
                                `Pesan baru dari ${
                                    sanitizeHTML(eventData.sender_name) ||
                                    "Admin"
                                }`,
                                eventData.message
                            );
                        }
                    }
                })
                .listen(".messages.read", (eventData) => {
                    if (
                        eventData.readerType === "admin" &&
                        eventData.userId == currentUserId
                    ) {
                        updateReadIndicators(eventData.lastReadMessageId);
                    }
                })
                .error((error) => {
                    console.error(
                        `[Echo] User subscription FAILED for ${channelName}`,
                        JSON.stringify(error)
                    );
                    echoChannel = null;
                });
        } catch (error) {
            console.error(
                `[ChatUser] Error subscribing Echo to ${channelName}:`,
                error
            );
            echoChannel = null;
        }
    }

    function updateReadIndicators(lastReadMessageId) {
        if (!chatMessagesElement) return;
        const userMessages = chatMessagesElement.querySelectorAll(
            ".d-flex.justify-content-end .chat-bubble"
        );
        userMessages.forEach((bubble) => {
            const messageWrapper = bubble.closest("[data-message-id]");
            if (!messageWrapper) return;
            const messageIdStr = messageWrapper.dataset.messageId;
            if (
                typeof messageIdStr === "string" &&
                messageIdStr.startsWith("optimistic_")
            )
                return;
            const messageId = parseInt(messageIdStr, 10);
            const readIndicator = bubble.querySelector(".read-indicator");

            if (
                readIndicator &&
                !readIndicator.classList.contains("fa-check-double") &&
                !readIndicator.classList.contains("fa-clock")
            ) {
                if (
                    !isNaN(messageId) &&
                    (lastReadMessageId === null ||
                        messageId <= lastReadMessageId)
                ) {
                    readIndicator.classList.remove("fa-check");
                    readIndicator.classList.add("fa-check-double", "text-info");
                }
            }
        });
    }

    function showDesktopNotification(title, body) {
        if (!("Notification" in window) || Notification.permission === "denied")
            return;
        if (Notification.permission === "granted") {
            new Notification(title, { body: body, icon: "/img/logo-gpib.png" });
        } else {
            Notification.requestPermission().then((p) => {
                if (p === "granted")
                    new Notification(title, {
                        body: body,
                        icon: "/img/logo-gpib.png",
                    });
            });
        }
    }

    // --- Event Listeners Setup ---
    if (chatMessageForm && isLoggedIn) {
        chatMessageForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const messageText = chatInputElement?.value.trim();
            if (messageText) sendMessage(messageText);
        });
    }

    chatModalElement.addEventListener("shown.bs.modal", () => {
        unreadMessagesCount = 0;
        updateChatIconBadge();
        if (templateContainer) templateContainer.innerHTML = "";
        if (templateLoadingIndicator)
            templateLoadingIndicator.style.display = "none";
        if (isLoggedIn) {
            currentPage = 1;
            hasMorePages = true;
            isLoadingHistory = false;
            if (chatMessagesElement) chatMessagesElement.innerHTML = "";
            if (chatLoadingIndicator)
                chatLoadingIndicator.style.display = "flex";
            fetchHistory(1);
            setupEchoListener();
        } else {
            if (chatMessagesElement) chatMessagesElement.innerHTML = "";
            if (chatLoadingIndicator)
                chatLoadingIndicator.style.display = "none";
        }
        fetchAndDisplayTemplates();
        if (isLoggedIn && chatInputElement) chatInputElement.focus();
    });

    chatModalElement.addEventListener("hidden.bs.modal", () => {
        chatModalElement.querySelector(":focus")?.blur();
    });

    if (chatMessagesElement && isLoggedIn) {
        const debounce = (func, delay) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => func.apply(this, a), delay);
            };
        };
        const handleScroll = () => {
            if (
                chatMessagesElement.scrollTop <= 5 &&
                hasMorePages &&
                !isLoadingHistory
            ) {
                fetchHistory(currentPage + 1);
            }
        };
        chatMessagesElement.addEventListener(
            "scroll",
            debounce(handleScroll, 300)
        );
    }
});
