jQuery(function ($) {
    // --- State Variables ---
    let currentUserId = null;
    let echoUserChannel = null;
    let echoAdminNotificationsChannel = null;
    let dataTableInstance = null;
    let isLoadingHistory = false;
    let currentHistoryPage = 1;
    let hasMoreHistoryPages = true;
    let markAsReadTimeout = null;

    // --- Element Selectors ---
    const chatDetailModal = $("#chatDetailModal");
    const adminChatMessagesContainer = $("#adminChatMessages");
    const adminChatMessageForm = $("#adminChatMessageForm");
    const adminChatInput = $("#adminChatInput");
    const adminChatLoadingIndicator = $("#adminChatLoadingIndicator");
    const chatDetailModalLabel = $("#chatDetailModalLabel");
    const chatUsersDataTable = $("#chatTable");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // --- Globals/Instances ---
    const apiClient = window.axios;
    const echoClient = window.Echo;
    const BsToast = window.bootstrap?.Toast;

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

    function showAdminNotification(message, type = "info") {
        if (!BsToast) {
            alert(message);
            return;
        }
        const toastId = `admin-notif-${Date.now()}`;
        const toastHtml = `
             <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1056;">
               <div class="d-flex"> <div class="toast-body"> ${sanitizeHTML(
                   message
               )} </div>
                 <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
               </div> </div>`;
        $("body").append(toastHtml);
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            const toast = new BsToast(toastElement);
            toast.show();
            toastElement.addEventListener("hidden.bs.toast", function () {
                $(this).remove();
            });
        }
    }

    function displayAdminChatMessage(message, prepend = false) {
        if (
            !adminChatMessagesContainer.length ||
            !message ||
            !message.sender_type
        )
            return;
        if (adminChatLoadingIndicator.length) adminChatLoadingIndicator.hide();
        adminChatMessagesContainer.find(".no-messages-placeholder").remove();

        const messageIdValue =
            message.id ??
            `optimistic_${Date.now()}_${Math.random()
                .toString(36)
                .substring(2, 9)}`;
        const messageIdStr = String(messageIdValue);

        if (
            !messageIdStr.startsWith("optimistic_") &&
            adminChatMessagesContainer.find(
                `[data-message-id="${messageIdStr}"]`
            ).length
        )
            return;

        const $messageWrapper = $("<div>")
            .addClass("mb-2 d-flex")
            .attr("data-message-id", messageIdStr);
        const isAdminSender = message.sender_type === "admin";
        $messageWrapper.addClass(
            isAdminSender ? "justify-content-end" : "justify-content-start"
        );

        const $bubble = $("<div>")
            .addClass("p-2 rounded mw-75 chat-bubble")
            .css({ "word-wrap": "break-word", "max-width": "75%" })
            .addClass(
                isAdminSender
                    ? "bg-primary text-white bubble-admin"
                    : "bg-secondary text-white bubble-user"
            );

        if (!isAdminSender && message.sender_name) {
            $("<small>")
                .addClass("d-block fw-bold mb-1 text-light sender-name")
                .text(sanitizeHTML(message.sender_name))
                .appendTo($bubble);
        }
        $("<div>")
            .addClass("message-body")
            .text(message.message)
            .appendTo($bubble);

        const $timestamp = $("<small>").addClass(
            "d-block mt-1 text-end opacity-75 message-timestamp"
        );
        $timestamp.text(formatTimestamp(message.created_at) + " ");

        if (isAdminSender) {
            const $readIndicator = $("<i>").addClass("fas read-indicator");
            if (message.read_at) {
                $readIndicator.addClass("fa-check-double text-info");
            } else if (messageIdStr.startsWith("optimistic_")) {
                $readIndicator.addClass("fa-clock text-warning");
            } else {
                $readIndicator.addClass("fa-check");
            }
            $timestamp.append($readIndicator);
        }
        $bubble.append($timestamp);
        $messageWrapper.append($bubble);

        if (prepend) adminChatMessagesContainer.prepend($messageWrapper);
        else adminChatMessagesContainer.append($messageWrapper);
    }

    function adminScrollToBottom(force = false) {
        if (!adminChatMessagesContainer.length) return;
        const container = adminChatMessagesContainer[0];
        const scrollThreshold = 150;
        const isNearBottom =
            container.scrollHeight -
                container.scrollTop -
                container.clientHeight <
            scrollThreshold;
        if (force || isNearBottom) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 50);
        }
    }

    function reloadDataTable() {
        if (dataTableInstance) dataTableInstance.ajax.reload(null, false);
    }

    async function fetchChatHistory(userId, page = 1) {
        if (isLoadingHistory && page > 1) return;
        isLoadingHistory = true;
        if (page === 1) {
            adminChatMessagesContainer.empty();
            if (adminChatLoadingIndicator.length)
                adminChatLoadingIndicator.show();
        }
        if (!apiClient) {
            isLoadingHistory = false;
            showAdminNotification("API Client tidak ditemukan.", "danger");
            return;
        }
        try {
            const url = `/dashboard/chat/history/${userId}?page=${page}`;
            const response = await apiClient.get(url);
            const historyData = response.data;
            if (page === 1 && adminChatLoadingIndicator.length)
                adminChatLoadingIndicator.hide();
            const messages = historyData.data;
            const currentScrollHeight =
                adminChatMessagesContainer.prop("scrollHeight");

            if (messages.length === 0 && page === 1) {
                adminChatMessagesContainer.html(
                    '<div class="text-center text-muted p-3 no-messages-placeholder">Belum ada pesan.</div>'
                );
            } else {
                messages.forEach((msg) => displayAdminChatMessage(msg, true));
            }
            if (page === 1) {
                setTimeout(() => {
                    markMessagesAsRead(userId);
                    adminScrollToBottom(true);
                }, 100);
            } else if (messages.length > 0) {
                adminChatMessagesContainer.scrollTop(
                    adminChatMessagesContainer.prop("scrollHeight") -
                        currentScrollHeight
                );
            }
            hasMoreHistoryPages = !!historyData.links.next;
            currentHistoryPage = historyData.meta.current_page;
        } catch (error) {
            console.error(
                `Workspace History Error user ${userId}:`,
                error.response?.data || error.message || error
            );
            if (page === 1) {
                if (adminChatLoadingIndicator.length)
                    adminChatLoadingIndicator.hide();
                adminChatMessagesContainer.html(
                    '<div class="text-center text-danger p-3">Gagal memuat riwayat chat.</div>'
                );
            }
            showAdminNotification("Gagal memuat riwayat chat.", "danger");
        } finally {
            isLoadingHistory = false;
        }
    }

    async function markMessagesAsRead(userId) {
        if (!userId || !apiClient) return;
        clearTimeout(markAsReadTimeout);
        markAsReadTimeout = setTimeout(async () => {
            try {
                const url = "/dashboard/chat/mark-read";
                await apiClient.post(url, { user_id: userId });
                reloadDataTable();
            } catch (error) {
                console.error(
                    `Mark Read Error user ${userId}:`,
                    error.response?.data || error.message
                );
            }
        }, 750);
    }

    function subscribeToUserChannel(userId) {
        if (!userId || !echoClient) return;
        leaveCurrentUserChannel();
        const channelName = `private-chat.user.${userId}`;
        try {
            echoUserChannel = echoClient.private(channelName);
            echoUserChannel
                .subscribed(() => {
                    console.log(`[Echo] Admin subscribed to ${channelName}`);
                })
                .listen(".message.sent", (eventData) => {
                    if (
                        eventData.user_id == currentUserId &&
                        chatDetailModal.hasClass("show")
                    ) {
                        if (eventData.sender_type === "admin") return;
                        if (
                            adminChatMessagesContainer.find(
                                `[data-message-id="${eventData.id}"]`
                            ).length === 0
                        ) {
                            displayAdminChatMessage(eventData, false);
                            adminScrollToBottom();
                            if (eventData.sender_type === "user")
                                markMessagesAsRead(currentUserId);
                        }
                    }
                })
                .listen(".messages.read", (eventData) => {
                    if (
                        eventData.userId == currentUserId &&
                        eventData.readerType === "user" &&
                        chatDetailModal.hasClass("show")
                    ) {
                        updateAdminReadIndicators(eventData.lastReadMessageId);
                    }
                })
                .error((error) => {
                    console.error(
                        `[Echo] Admin subscription FAILED for ${channelName}`,
                        JSON.stringify(error)
                    );
                    showAdminNotification(
                        `Gagal koneksi real-time ke user ${userId}.`,
                        "danger"
                    );
                    echoUserChannel = null;
                });
        } catch (error) {
            console.error(`Error subscribing Echo to ${channelName}:`, error);
            showAdminNotification(
                "Gagal inisialisasi listener chat user.",
                "danger"
            );
        }
    }

    function updateAdminReadIndicators(lastReadMessageId) {
        if (!adminChatMessagesContainer.length) return;
        adminChatMessagesContainer.find(".bubble-admin").each(function () {
            const $bubble = $(this);
            const $messageWrapper = $bubble.closest("[data-message-id]");
            if (!$messageWrapper.length) return;
            const messageIdStr = $messageWrapper.data("messageId");
            if (
                typeof messageIdStr === "string" &&
                messageIdStr.startsWith("optimistic_")
            )
                return;
            const messageId = parseInt(messageIdStr, 10);
            const $readIndicator = $bubble.find(".read-indicator");

            if (
                $readIndicator.length &&
                !$readIndicator.hasClass("fa-check-double") &&
                !$readIndicator.hasClass("fa-clock")
            ) {
                if (
                    !isNaN(messageId) &&
                    (lastReadMessageId === null ||
                        messageId <= lastReadMessageId)
                ) {
                    $readIndicator
                        .removeClass("fa-check")
                        .addClass("fa-check-double text-info");
                }
            }
        });
    }

    function leaveCurrentUserChannel() {
        if (echoUserChannel && echoClient) {
            try {
                echoClient.leave(echoUserChannel.name);
            } catch (e) {}
            echoUserChannel = null;
        }
    }

    // --- Initialization & Global Listeners ---
    if (apiClient && csrfToken) {
        apiClient.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
        apiClient.defaults.headers.common["X-Requested-With"] =
            "XMLHttpRequest";
        apiClient.defaults.headers.common["Accept"] = "application/json";
    } else if (!apiClient) {
        showAdminNotification("Axios tidak tersedia.", "danger");
    }

    if ($.fn.DataTable.isDataTable("#chatTable"))
        $("#chatTable").DataTable().destroy();
    dataTableInstance = chatUsersDataTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/dashboard/chat/users-datatable",
            type: "GET",
            error: function (xhr, et, thr) {
                console.error("DT Error:", et, thr, xhr.responseText);
                showAdminNotification("Gagal memuat daftar chat.", "danger");
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
            { data: "aksi", name: "aksi", orderable: false, searchable: false },
        ],
    });

    function setupAdminNotificationListener() {
        if (!echoClient) return;
        if (echoAdminNotificationsChannel) return;
        const channelName = "admin-notifications";
        try {
            echoAdminNotificationsChannel = echoClient.private(channelName);
            echoAdminNotificationsChannel
                .subscribed(() => {
                    console.log(`[Echo] Admin subscribed to ${channelName}`);
                })
                .listen(".new.user.message", (eventData) => {
                    if (
                        !(
                            currentUserId == eventData.user_id &&
                            chatDetailModal.hasClass("show")
                        )
                    ) {
                        reloadDataTable();
                        showAdminNotification(
                            `Pesan baru dari ${
                                sanitizeHTML(eventData.user_name) ||
                                "User " + eventData.user_id
                            }: ${sanitizeHTML(eventData.message_preview)}`
                        );
                    }
                })
                .error((error) => {
                    console.error(
                        `[Echo] Admin subscription FAILED for ${channelName}`,
                        JSON.stringify(error)
                    );
                    showAdminNotification(
                        "Gagal koneksi ke notifikasi admin.",
                        "danger"
                    );
                    echoAdminNotificationsChannel = null;
                });
        } catch (error) {
            console.error("[setupAdminNotificationListener] Error:", error);
            showAdminNotification(
                "Gagal inisialisasi listener notifikasi admin.",
                "danger"
            );
        }
    }
    setupAdminNotificationListener();

    // --- Event Handlers ---
    chatUsersDataTable.on("click", ".openChatModal", function () {
        const conversationData = $(this).data("conversation");
        if (conversationData && conversationData.user_id) {
            currentUserId = conversationData.user_id;
            const modalTitle = `Chat dengan ${
                sanitizeHTML(conversationData.user_name) ||
                `User ID: ${currentUserId}`
            }`;
            if (chatDetailModalLabel.length)
                chatDetailModalLabel.text(modalTitle);
            adminChatMessagesContainer.empty();
            if (adminChatLoadingIndicator.length)
                adminChatLoadingIndicator.show();
            currentHistoryPage = 1;
            hasMoreHistoryPages = true;
            isLoadingHistory = false;
            fetchChatHistory(currentUserId, 1);
            subscribeToUserChannel(currentUserId);
        } else {
            showAdminNotification("Data pengguna chat tidak valid.", "warning");
        }
    });

    if (adminChatMessageForm.length) {
        adminChatMessageForm.on("submit", function (event) {
            event.preventDefault();
            const messageText = adminChatInput.val().trim();
            if (messageText && currentUserId)
                sendAdminMessage(currentUserId, messageText);
            else if (!currentUserId)
                showAdminNotification(
                    "Tidak ada chat user yang aktif.",
                    "warning"
                );
        });
    }

    async function sendAdminMessage(targetUserId, messageText) {
        if (!targetUserId || !messageText || !apiClient) return;
        const optimisticId = `optimistic_${Date.now()}_${Math.random()
            .toString(36)
            .substring(2, 9)}`;
        displayAdminChatMessage(
            {
                id: optimisticId,
                sender_type: "admin",
                sender_name: "Admin Gereja",
                message: messageText,
                created_at: new Date().toISOString(),
                read_at: null,
            },
            false
        );
        adminScrollToBottom(true);
        adminChatInput.val("");
        try {
            const url = "/dashboard/chat/send-admin";
            const payload = {
                message: messageText,
                target_user_id: targetUserId,
            };
            const response = await apiClient.post(url, payload);
            const serverMessageData = response.data.data;
            const $optimisticElement = adminChatMessagesContainer.find(
                `[data-message-id="${optimisticId}"]`
            );
            if ($optimisticElement.length && serverMessageData) {
                $optimisticElement.attr(
                    "data-message-id",
                    String(serverMessageData.id)
                );
                const $timestamp =
                    $optimisticElement.find(".message-timestamp");
                const $indicator = $optimisticElement.find(".read-indicator");
                if ($timestamp.length && $indicator.length) {
                    $timestamp.text(
                        formatTimestamp(serverMessageData.created_at) + " "
                    );
                    $indicator
                        .removeClass("fa-clock text-warning")
                        .addClass("fa-check");
                    $timestamp.append($indicator);
                }
            } else if (!$optimisticElement.length && serverMessageData) {
                displayAdminChatMessage(serverMessageData, false);
            }
            reloadDataTable();
        } catch (error) {
            console.error(
                "Send Admin Message Error:",
                error.response?.data || error.message || error
            );
            adminChatMessagesContainer
                .find(`[data-message-id="${optimisticId}"]`)
                .remove();
            showAdminNotification(
                `Gagal mengirim pesan: ${
                    error.response?.data?.message || error.message
                }`,
                "danger"
            );
            adminChatInput.val(messageText);
        }
    }

    if (adminChatMessagesContainer.length) {
        const debounce = (func, delay) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => func.apply(this, a), delay);
            };
        };
        const handleAdminScroll = function () {
            if (
                this.scrollTop <= 5 &&
                hasMoreHistoryPages &&
                !isLoadingHistory &&
                currentUserId
            ) {
                fetchChatHistory(currentUserId, currentHistoryPage + 1);
            }
        };
        adminChatMessagesContainer.on(
            "scroll",
            debounce(handleAdminScroll, 300)
        );
    }

    if (chatDetailModal.length) {
        chatDetailModal.on("hidden.bs.modal", () => {
            leaveCurrentUserChannel();
            currentUserId = null;
            chatDetailModal.find(":focus").trigger("blur");
        });
        chatDetailModal.on("shown.bs.modal", () => {
            if (adminChatInput.length) adminChatInput.trigger("focus");
        });
    }
});
