<button type="button" class="btn btn-primary btn-icon-split mb-4" id="btn-add-renungan">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Renungan Baru</span>
</button>

<div class="modal fade" id="renunganModal" tabindex="-1" aria-labelledby="renunganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renunganModalLabel">Form Renungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="renunganForm" name="renunganForm" enctype="multipart/form-data">
                    <input type="hidden" name="renungan_id" id="renungan_id">
                    <input type="hidden" name="_method" id="form_method">

                    <div class="alert alert-danger d-none" id="error-alert" role="alert"></div>

                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Renungan</label>
                        <input type="text" class="form-control" id="judul" name="judul"
                            placeholder="Masukkan judul renungan" required aria-describedby="judul-error">
                        <div class="invalid-feedback" id="judul-error"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="alkitab" class="form-label">Alkitab (Opsional)</label>
                            <input type="text" class="form-control" id="alkitab" name="alkitab"
                                placeholder="Contoh: Yohanes 3:16" aria-describedby="alkitab-error">
                            <div class="invalid-feedback" id="alkitab-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bacaan_alkitab" class="form-label">Tema Bacaan (Opsional)</label>
                            <input type="text" class="form-control" id="bacaan_alkitab" name="bacaan_alkitab"
                                placeholder="Contoh: Kasih Allah" aria-describedby="bacaan_alkitab-error">
                            <div class="invalid-feedback" id="bacaan_alkitab-error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail (Opsional, Max: 16MB)</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*"
                            aria-describedby="thumbnail-error">
                        <div id="thumbnail-error" class="invalid-feedback d-block"></div>
                        <img src="#" alt="Preview Thumbnail" class="img-fluid mt-2 d-none" id="thumbnail-preview"
                            style="max-height: 150px;">
                        <small id="current-thumbnail-info" class="form-text text-muted d-none mt-1">Thumbnail saat ini:
                            <span id="current-thumbnail-name"></span></small>
                    </div>

                    <div class="mb-3">
                        <label for="isi_bacaan" class="form-label">Isi Renungan</label>
                        <textarea class="form-control" id="isi_bacaan" name="isi_bacaan" rows="10"
                            placeholder="Tulis isi renungan di sini..." required aria-describedby="isi_bacaan-error"></textarea>
                        <div class="invalid-feedback" id="isi_bacaan-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save">Simpan</button>
            </div>
        </div>
    </div>
</div>
