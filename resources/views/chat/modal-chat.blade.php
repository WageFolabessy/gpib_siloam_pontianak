<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true"
    @auth('web') data-user-id="{{ Auth::id() }}" @endauth>

    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="chatModalLabel">Live Chat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-3">

                @guest('web')
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Agar dapat menikmati fitur chat secara penuh dan menyimpan riwayat percakapan,
                        silakan <a href="{{ route('login_jemaat') }}" class="alert-link">login</a>
                        terlebih dahulu. Tanpa login, Anda hanya dapat mengakses pesan template dan chat hanya akan
                        tersimpan sementara (chat akan hilang saat Anda menutup jendela chat).
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endguest

                <div id="chatMessages"
                    style="min-height: 200px; max-height: 400px; overflow-y: auto; background: #eaeaea; padding: 15px; border: 1px solid #e0e0e0; border-radius: 5px;">
                    <div class="text-center text-muted py-3" id="chatLoadingIndicator">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        Memuat percakapan...
                    </div>
                </div>

                <div id="templateTanyaJawab" class="mt-3">
                    <div class="text-center text-muted py-2" id="templateLoadingIndicator">
                        Memuat template...
                    </div>
                </div>
            </div>

            @auth('web')
                <div class="modal-footer border-top-0">
                    <form id="chatMessageForm" class="w-100" novalidate>
                        <div class="input-group">
                            <input type="text" id="chatInput" class="form-control" placeholder="Ketik pesan Anda..."
                                autocomplete="off" required>
                            <button type="submit" class="btn btn-primary" id="sendChat">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            @endauth

        </div>
    </div>
</div>
