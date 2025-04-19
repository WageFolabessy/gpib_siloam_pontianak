// assets/dashboard/js/chat-admin.js (Versi Real-time Bersih)

jQuery(function ($) {
    // =============================================
    // === BAGIAN HELPER FUNCTIONS ===
    // =============================================
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

    function debounce(func, wait) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // =============================================
    // === BAGIAN STATE & ELEMENT DOM ===
    // =============================================
    let currentOpenChatUserId = null;
    let currentUserEchoChannel = null; // Channel untuk user yg modalnya dibuka
    let adminNotificationChannel = null; // Channel global admin
    let adminChatDataTable = null;
    let isLoadingAdminHistory = false;
    let currentAdminChatPage = 1;
    let hasMoreAdminHistory = true;
    let markAsReadTimer = null;

    const $adminChatModal = $("#chatDetailModal");
    const $adminChatMessagesContainer = $("#adminChatMessages");
    const $adminChatMessageForm = $("#adminChatMessageForm");
    const $adminChatInput = $("#adminChatInput");
    const $adminChatLoadingIndicator = $("#adminChatLoadingIndicator");
    const $adminModalTitle = $("#chatDetailModalLabel");

    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    const apiClient = window.axios;

    if (apiClient && csrfToken) {
        apiClient.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
        apiClient.defaults.headers.common["X-Requested-With"] =
            "XMLHttpRequest";
        apiClient.defaults.headers.common["Accept"] = "application/json";
        apiClient.defaults.headers.common["Content-Type"] = "application/json";
    } else if (!apiClient) {
        console.error(
            "Axios (apiClient) is not available. Real-time features might fail."
        );
        showErrorNotification("Komponen penting (Axios) tidak ditemukan.");
    }

    // =============================================
    // === FUNGSI UI (Display, Scroll, Notifikasi) ===
    // =============================================

    function displayAdminChatMessage(messageData, prepend = false) {
        if (
            !$adminChatMessagesContainer.length ||
            !messageData ||
            !messageData.sender_type
        ) {
            return;
        }
        if ($adminChatLoadingIndicator.length)
            $adminChatLoadingIndicator.hide();
        $adminChatMessagesContainer.find(".no-messages-placeholder").remove();

        const msgId = messageData.id || `optimistic_${Date.now()}`;
        const msgDiv = $("<div>")
            .addClass("mb-2 d-flex")
            .attr("data-message-id", msgId);
        const senderIsAdmin = messageData.sender_type === "admin";

        msgDiv.addClass(
            senderIsAdmin ? "justify-content-end" : "justify-content-start"
        );

        const bubbleDiv = $("<div>")
            .addClass("p-2 rounded mw-75 chat-bubble")
            .css({ "word-wrap": "break-word", "max-width": "75%" })
            .addClass(
                senderIsAdmin
                    ? "bg-primary text-white bubble-admin"
                    : "bg-secondary text-white bubble-user"
            );

        if (!senderIsAdmin && messageData.sender_name) {
            const senderNameSpan = $("<small>")
                .addClass("d-block fw-bold mb-1 text-light sender-name")
                .text(escapeHTML(messageData.sender_name));
            bubbleDiv.append(senderNameSpan);
        }

        const messageText = $("<div>")
            .addClass("message-body")
            .text(messageData.message);
        bubbleDiv.append(messageText);

        const timeSpan = $("<small>").addClass(
            "d-block mt-1 text-end opacity-75 message-timestamp"
        );
        timeSpan.text(formatTimestamp(messageData.created_at) + " ");

        if (senderIsAdmin) {
            const readIndicator = $("<i>").addClass("bi read-indicator");
            if (messageData.read_at) {
                readIndicator.addClass("bi-check2-all text-info");
            } else if (!String(msgId).startsWith("optimistic_")) {
                readIndicator.addClass("bi-check2");
            } else {
                readIndicator.addClass("bi-clock text-warning");
            }
            timeSpan.append(readIndicator);
        }
        bubbleDiv.append(timeSpan);
        msgDiv.append(bubbleDiv);

        if (prepend) {
            $adminChatMessagesContainer.prepend(msgDiv);
        } else {
            $adminChatMessagesContainer.append(msgDiv);
        }
    }

    function scrollAdminChatToBottom(force = false) {
        if ($adminChatMessagesContainer.length) {
            const container = $adminChatMessagesContainer[0];
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

    function updateAdminMessagesReadStatus(readerType, lastReadMessageId) {
        if (!$adminChatMessagesContainer.length || readerType !== "user")
            return;
        $adminChatMessagesContainer.find(".bubble-admin").each(function () {
            const $bubble = $(this);
            const $msgDiv = $bubble.closest("[data-message-id]");
            const messageId = parseInt($msgDiv.data("messageId"), 10);
            const $indicator = $bubble.find(".read-indicator");

            if (
                $indicator.length &&
                !$indicator.hasClass("text-info") &&
                !$indicator.hasClass("bi-clock")
            ) {
                if (
                    lastReadMessageId === null ||
                    (messageId && messageId <= lastReadMessageId)
                ) {
                    $indicator
                        .removeClass("bi-check2")
                        .addClass("bi-check2-all text-info");
                }
            }
        });
    }

    // --- Fungsi Notifikasi Placeholder ---
    function showInfoNotification(message) {
        console.info("Notification:", message);
        // Ganti dengan implementasi notifikasi Anda (Toastr, SweetAlert, dll)
        // Contoh sederhana:
        const notifId = `notif-${Date.now()}`;
        const notifDiv = $(
            `<div id="${notifId}" class="toast align-items-center text-bg-info border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1056;"></div>`
        );
        const flexDiv = $('<div class="d-flex"></div>');
        const bodyDiv = $('<div class="toast-body"></div>').text(message);
        const closeButton = $(
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        );
        flexDiv.append(bodyDiv).append(closeButton);
        notifDiv.append(flexDiv);
        $("body").append(notifDiv);
        const toast = new bootstrap.Toast(document.getElementById(notifId));
        toast.show();
        // Hapus elemen setelah toast hilang
        document
            .getElementById(notifId)
            .addEventListener("hidden.bs.toast", function () {
                $(this).remove();
            });
    }

    function showErrorNotification(message) {
        console.error("Notification Error:", message);
        // Ganti dengan implementasi notifikasi Anda
        alert(`Error: ${message}`); // Fallback
    }

    // --- API Calls ---
    async function fetchChatHistoryForUser(userId, page = 1) {
        if (isLoadingAdminHistory && page !== 1) return;
        isLoadingAdminHistory = true;

        if (page === 1) {
            $adminChatMessagesContainer.empty();
            if ($adminChatLoadingIndicator.length)
                $adminChatLoadingIndicator.show();
        }

        if (!apiClient) {
            isLoadingAdminHistory = false;
            if ($adminChatLoadingIndicator.length)
                $adminChatLoadingIndicator.hide();
            $adminChatMessagesContainer.html(
                '<div class="text-center text-danger p-3">Gagal memuat: API Client tidak ada.</div>'
            );
            return;
        }

        try {
            const url = `/dashboard/chat/history/${userId}?page=${page}`;
            const response = await apiClient.get(url);
            const paginatedData = response.data;

            if ($adminChatLoadingIndicator.length && page === 1)
                $adminChatLoadingIndicator.hide();

            const messages = paginatedData.data;
            const initialScrollHeight =
                $adminChatMessagesContainer.prop("scrollHeight");

            if (messages.length === 0 && page === 1) {
                $adminChatMessagesContainer.html(
                    '<div class="text-center text-muted p-3 no-messages-placeholder">Belum ada pesan.</div>'
                );
            } else {
                messages
                    .reverse()
                    .forEach((message) =>
                        displayAdminChatMessage(message, true)
                    );
            }

            if (page === 1) {
                setTimeout(() => {
                    markUserMessagesAsRead(userId);
                    scrollAdminChatToBottom(true);
                }, 100);
            } else if (messages.length > 0) {
                $adminChatMessagesContainer.scrollTop(
                    $adminChatMessagesContainer.prop("scrollHeight") -
                        initialScrollHeight
                );
            }

            hasMoreAdminHistory = !!paginatedData.links.next;
            currentAdminChatPage = paginatedData.meta.current_page;
        } catch (error) {
            console.error(
                `[fetchChatHistory] Error user ${userId}:`,
                error.response?.data || error.message || error
            );
            if (page === 1) {
                if ($adminChatLoadingIndicator.length)
                    $adminChatLoadingIndicator.hide();
                $adminChatMessagesContainer.html(
                    '<div class="text-center text-danger p-3">Gagal memuat riwayat chat.</div>'
                );
            }
            showErrorNotification("Gagal memuat riwayat chat.");
        } finally {
            isLoadingAdminHistory = false;
        }
    }

    async function sendAdminMessageToServer(targetUserId, messageText) {
        if (!targetUserId || !messageText || !apiClient) return;

        const optimisticId = `optimistic_${Date.now()}`;
        const optimisticMessageData = {
            id: optimisticId,
            sender_type: "admin",
            sender_name: "Admin Gereja",
            message: messageText,
            created_at: new Date().toISOString(),
            read_at: null,
        };
        displayAdminChatMessage(optimisticMessageData);
        scrollAdminChatToBottom(true);

        try {
            const url = "/dashboard/chat/send-admin";
            const payload = {
                message: messageText,
                target_user_id: targetUserId,
            };
            const response = await apiClient.post(url, payload);
            const serverMessageData = response.data.data;

            const $optimisticMessage = $(`[data-message-id="${optimisticId}"]`);
            if ($optimisticMessage.length && serverMessageData) {
                $optimisticMessage.attr(
                    "data-message-id",
                    serverMessageData.id
                );
                $optimisticMessage
                    .find(".read-indicator")
                    .removeClass("bi-clock text-warning")
                    .addClass("bi-check2");
                const $timestampSpan =
                    $optimisticMessage.find(".message-timestamp");
                if ($timestampSpan.length) {
                    $timestampSpan.text(
                        formatTimestamp(serverMessageData.created_at) + " "
                    );
                    const readIndicator = $("<i>").addClass(
                        "bi bi-check2 read-indicator"
                    );
                    $timestampSpan.append(readIndicator);
                }
            } else {
                $optimisticMessage.remove();
                if (serverMessageData)
                    displayAdminChatMessage(serverMessageData);
            }
            refreshChatTable();
        } catch (error) {
            console.error(
                "[sendAdminMessage] Error:",
                error.response?.data || error.message || error
            );
            $(`[data-message-id="${optimisticId}"]`).remove();
            showErrorNotification(
                `Gagal mengirim pesan: ${
                    error.response?.data?.message || error.message
                }`
            );
        }
    }

    async function markUserMessagesAsRead(userId) {
        if (!userId || !apiClient) return;
        clearTimeout(markAsReadTimer);

        markAsReadTimer = setTimeout(async () => {
            try {
                const url = "/chat/mark-read";
                const payload = { user_id: userId };
                await apiClient.post(url, payload);
                refreshChatTable();
            } catch (error) {
                console.error(
                    `[markUserMessagesAsRead] Error user ${userId}:`,
                    error.response?.data || error.message || error
                );
            }
        }, 750);
    }

    // --- Echo Functions ---

    function initializeAdminNotificationListener() {
        if (typeof window.Echo === "undefined" || !apiClient) return;
        if (adminNotificationChannel) return;

        const channelName = "admin-notifications";
        try {
            adminNotificationChannel = window.Echo.private(channelName);
            adminNotificationChannel
                .subscribed(() => {
                    console.log(`[Echo] Subscribed to ${channelName}`);
                })
                .listen(".new.user.message", (event) => {
                    const isUserModalOpen =
                        currentOpenChatUserId == event.user_id &&
                        $adminChatModal.hasClass("show");
                    if (!isUserModalOpen) {
                        refreshChatTable();
                        showInfoNotification(
                            `Pesan baru dari ${
                                escapeHTML(event.user_name) ||
                                "User " + event.user_id
                            }: ${escapeHTML(event.message_preview)}`
                        );
                    }
                })
                .error((error) => {
                    console.error(
                        `[Echo] Subscription FAILED for ${channelName}`,
                        JSON.stringify(error)
                    );
                    showErrorNotification(
                        `Gagal terhubung ke channel notifikasi admin.`
                    );
                    adminNotificationChannel = null;
                });
        } catch (e) {
            console.error("[Admin Listener] Error subscribing:", e);
            showErrorNotification(
                "Gagal menginisialisasi listener notifikasi admin."
            );
        }
    }

    function subscribeToUserChannel(userId) {
        if (!userId || typeof window.Echo === "undefined" || !apiClient) return;
        leaveCurrentUserChannel();

        const channelName = `private-chat.user.${userId}`;
        try {
            currentUserEchoChannel = window.Echo.private(channelName);
            currentUserEchoChannel
                .subscribed(() => {
                    console.log(
                        `[Echo] Subscribed to user channel ${channelName}`
                    );
                })
                .listen(".message.sent", (event) => {
                    if (
                        event.user_id == currentOpenChatUserId &&
                        $adminChatModal.hasClass("show")
                    ) {
                        const $existingMessage = $(
                            `[data-message-id="${event.id}"]`
                        );
                        if ($existingMessage.length > 0) {
                            if (
                                $existingMessage.find(".bi-clock").length > 0 &&
                                event.sender_type === "admin"
                            ) {
                                $existingMessage
                                    .find(".read-indicator")
                                    .removeClass("bi-clock text-warning")
                                    .addClass("bi-check2");
                            }
                        } else {
                            displayAdminChatMessage(event);
                            scrollAdminChatToBottom();
                            if (event.sender_type === "user") {
                                markUserMessagesAsRead(currentOpenChatUserId);
                            }
                        }
                    }
                })
                .listen(".messages.read", (event) => {
                    if (
                        event.user_id == currentOpenChatUserId &&
                        event.readerType === "user" &&
                        $adminChatModal.hasClass("show")
                    ) {
                        updateAdminMessagesReadStatus(
                            event.readerType,
                            event.lastReadMessageId
                        );
                    }
                })
                .error((error) => {
                    console.error(
                        `[Echo] Subscription FAILED for ${channelName}`,
                        JSON.stringify(error)
                    );
                    showErrorNotification(
                        `Gagal terhubung ke real-time chat user ${userId}.`
                    );
                    currentUserEchoChannel = null;
                });
        } catch (e) {
            console.error("[User Listener] Error subscribing:", e);
            showErrorNotification("Gagal menginisialisasi listener chat user.");
        }
    }

    function leaveCurrentUserChannel() {
        if (currentUserEchoChannel) {
            try {
                window.Echo.leave(currentUserEchoChannel.name);
            } catch (e) {
                console.error("[leaveCurrentUserChannel] Error:", e);
            } finally {
                currentUserEchoChannel = null;
            }
        }
    }

    // --- DataTables ---
    function initializeDataTable() {
        if ($.fn.DataTable.isDataTable("#chatTable")) {
            $("#chatTable").DataTable().destroy();
            $("#chatTable tbody").empty();
        }
        adminChatDataTable = $("#chatTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/dashboard/chat/users-datatable",
                type: "GET",
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(
                        "DataTables AJAX Error:",
                        textStatus,
                        errorThrown,
                        jqXHR.responseText
                    );
                    showErrorNotification("Gagal memuat daftar chat.");
                    $("#chatTable tbody").html(
                        '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data. Coba refresh.</td></tr>'
                    );
                },
            },
            order: [[3, "desc"]],
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                },
                { data: "nama_pengguna", name: "users.name" },
                {
                    data: "pesan_terakhir",
                    name: "last_chat.message",
                    orderable: false,
                    searchable: false,
                },
                { data: "waktu", name: "chats.created_at" },
                {
                    data: "unread",
                    name: "unread_user_messages_count",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                },
            ],
        });
    }

    function refreshChatTable() {
        if (adminChatDataTable) {
            adminChatDataTable.ajax.reload(null, false);
        }
    }

    // --- Event Listeners ---

    initializeDataTable();
    initializeAdminNotificationListener(); // <-- Inisialisasi listener global

    $("#chatTable tbody").on("click", ".openChatModal", function () {
        const conversationData = $(this).data("conversation");
        if (conversationData && conversationData.user_id) {
            currentOpenChatUserId = conversationData.user_id;
            const userName = conversationData.user_name;
            const titleText = `Chat dengan ${
                escapeHTML(userName) || "User ID: " + currentOpenChatUserId
            }`;
            if ($adminModalTitle.length) $adminModalTitle.text(titleText);

            if ($adminChatMessagesContainer.length)
                $adminChatMessagesContainer.empty();
            if ($adminChatLoadingIndicator.length)
                $adminChatLoadingIndicator.show();
            currentAdminChatPage = 1;
            hasMoreAdminHistory = true;
            isLoadingAdminHistory = false;

            fetchChatHistoryForUser(currentOpenChatUserId, 1);
            subscribeToUserChannel(currentOpenChatUserId); // <-- Subscribe ke channel user saat modal dibuka
        } else {
            showErrorNotification("Data pengguna untuk chat ini tidak valid.");
        }
    });

    if ($adminChatMessageForm.length) {
        $adminChatMessageForm.on("submit", (event) => {
            event.preventDefault();
            const messageText = $adminChatInput.val().trim();
            if (messageText && currentOpenChatUserId) {
                sendAdminMessageToServer(currentOpenChatUserId, messageText);
                $adminChatInput.val("");
            } else if (!currentOpenChatUserId) {
                showErrorNotification("Tidak ada chat user yang aktif.");
            }
        });
    }

    if ($adminChatMessagesContainer.length) {
        $adminChatMessagesContainer.on(
            "scroll",
            debounce(function () {
                if (
                    this.scrollTop === 0 &&
                    hasMoreAdminHistory &&
                    !isLoadingAdminHistory &&
                    currentOpenChatUserId
                ) {
                    fetchChatHistoryForUser(
                        currentOpenChatUserId,
                        currentAdminChatPage + 1
                    );
                }
            }, 300)
        );
    }

    if ($adminChatModal.length) {
        $adminChatModal.on("hidden.bs.modal", () => {
            leaveCurrentUserChannel(); // <-- Unsubscribe dari channel user saat modal ditutup
            currentOpenChatUserId = null;
            $adminChatModal.find(":focus").trigger("blur");
        });
        $adminChatModal.on("shown.bs.modal", () => {
            if ($adminChatInput.length) $adminChatInput.trigger("focus");
        });
    }
}); // Akhir jQuery ready
