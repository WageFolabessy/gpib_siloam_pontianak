<div class="modal fade" id="chatDetailModal" tabindex="-1" aria-labelledby="chatDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatDetailModalLabel">Detail Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Area pesan chat: pesan asli akan ditampilkan secara dinamis -->
                <div id="chatMessages" style="max-height:400px; overflow-y:auto; padding:10px;">
                    <!-- Pesan akan di-load dari localStorage melalui JS -->
                </div>
            </div>
            <div class="modal-footer">
                <!-- Form untuk mengirim pesan admin -->
                <form action="#" method="POST" class="w-100">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="reply" class="form-control" placeholder="Ketik balasan..."
                            required>
                        <button class="btn btn-primary" type="submit">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
