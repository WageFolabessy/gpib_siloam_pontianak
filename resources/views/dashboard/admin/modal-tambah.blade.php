{{-- Tombol Trigger Modal Tambah --}}
<button type="button" class="btn btn-primary btn-icon-split mb-4" id="btn-add-admin">
    <span class="icon text-white-50">
        <i class="fas fa-user-plus"></i>
    </span>
    <span class="text">Admin Baru</span>
</button>

{{-- Modal untuk Tambah/Edit Admin --}}
<div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminModalLabel">Form Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form --}}
                <form id="adminForm" name="adminForm">
                    <input type="hidden" name="admin_id" id="admin_id">
                    <input type="hidden" name="_method" id="form_method">

                    <div class="alert alert-danger d-none" id="error-alert" role="alert"></div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Masukkan username unik" required aria-describedby="username-error"
                            autocomplete="username">
                        <div class="invalid-feedback" id="username-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Masukkan password (min. 8 karakter)"
                            aria-describedby="password-error password-help" autocomplete="new-password">
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" placeholder="Ulangi password"
                            aria-describedby="password_confirmation-error" autocomplete="new-password">
                        <div class="invalid-feedback" id="password_confirmation-error"></div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-admin">Simpan</button>
            </div>
        </div>
    </div>
</div>
