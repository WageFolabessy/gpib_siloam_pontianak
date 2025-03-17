<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                        Agar dapat menikmati fitur chat secara penuh dan menyimpan riwayat percakapan,
                        silakan <a href="{{ route('pages.login') }}" class="alert-link">login</a> terlebih dahulu.
                        Tanpa login, Anda hanya dapat mengakses pesan template dan chat hanya akan tersimpan sementara
                        (chat akan hilang saat Anda menutup jendela chat).
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Area Chat: Pesan akan ditampilkan di sini -->
                <div id="chatMessages"
                    style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 15px; border: 1px solid #e0e0e0; border-radius: 5px;">
                    <!-- Pesan akan di-load dari localStorage via JS -->
                </div>
                <!-- Container untuk daftar template tanya jawab -->
                <div id="templateTanyaJawab" class="mt-3">
                    <!-- Template akan di-load secara dinamis -->
                </div>
            </div>
            @if (Auth::check())
                <div class="modal-footer border-top-0">
                    <div class="input-group">
                        <input type="text" id="chatInput" class="form-control" placeholder="Ketik pesan Anda...">
                        <button type="button" class="btn btn-primary" id="sendChat">Kirim</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
