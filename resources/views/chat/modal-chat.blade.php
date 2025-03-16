<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="chatModalLabel">Live Chat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Area Chat: Pesan akan ditampilkan di sini -->
                <div id="chatMessages">
                    <!-- Pesan akan di-load dari localStorage via JS -->
                </div>
                <!-- Container untuk daftar template tanya jawab -->
                <div id="templateTanyaJawab" class="mt-3">
                    <!-- Template akan di-load secara dinamis -->
                </div>
            </div>
            <div class="modal-footer">
                <div class="input-group">
                    <input type="text" id="chatInput" class="form-control" placeholder="Ketik pesan Anda...">
                    <button type="button" class="btn btn-primary" id="sendChat">Kirim</button>
                </div>
            </div>
        </div>
    </div>
</div>
