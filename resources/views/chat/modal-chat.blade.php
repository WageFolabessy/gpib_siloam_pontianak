<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="chatModalLabel">Live Chat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                {{-- Jika pengguna belum login, tampilkan peringatan --}}
                @if (!Auth::check())
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Untuk menggunakan fitur chat lengkap dan untuk menyimpan chat, silakan
                        <a href="{{ route('pages.login') }}" class="alert-link">login</a>.
                        Jika tidak, Anda hanya dapat mengirim pesan template saja dan chat Anda hanya tersimpan
                        sementara dan akan hilang setelah menutup chat.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Area Chat: Pesan akan ditampilkan di sini -->
                <div id="chatMessages">
                    <!-- Pesan akan di-load dari localStorage via JS -->
                </div>
                <!-- Container untuk daftar template tanya jawab -->
                <div id="templateTanyaJawab" class="mt-3">
                    <!-- Template akan di-load secara dinamis -->
                </div>
            </div>
            @if (Auth::check())
                <div class="modal-footer">
                    <div class="input-group">
                        <input type="text" id="chatInput" class="form-control" placeholder="Ketik pesan Anda...">
                        <button type="button" class="btn btn-primary" id="sendChat">Kirim</button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
