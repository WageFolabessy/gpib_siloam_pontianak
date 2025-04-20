<div class="modal fade" id="chatDetailModal" tabindex="-1" aria-labelledby="chatDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatDetailModalLabel">Detail Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Area pesan chat --}}
                <div id="adminChatMessages"
                    style="min-height: 300px; max-height:450px; overflow-y:auto; padding:10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: .25rem;">
                    <div class="text-center text-muted py-5" id="adminChatLoadingIndicator">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Memuat Pesan...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{-- Form untuk mengirim pesan admin --}}
                <form id="adminChatMessageForm" class="w-100" novalidate>
                    <div class="input-group">
                        <input type="text" id="adminChatInput" name="reply" class="form-control"
                            placeholder="Ketik balasan..." autocomplete="off" required>
                        <button class="btn btn-primary" type="submit" id="adminSendChatButton">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
