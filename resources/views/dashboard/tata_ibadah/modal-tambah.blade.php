<button type="button" class="btn btn-primary btn-icon-split mb-4" id="btn-add-tata-ibadah">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Tata Ibadah Baru</span>
</button>

<div class="modal fade" id="tataIbadahModal" tabindex="-1" aria-labelledby="tataIbadahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tataIbadahModalLabel">Form Tata Ibadah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tataIbadahForm" name="tataIbadahForm" enctype="multipart/form-data">
                    <input type="hidden" name="tata_ibadah_id" id="tata_ibadah_id">

                    <div class="alert alert-danger d-none" id="error-alert-ti" role="alert"></div>

                    <div class="mb-3">
                        <label for="judul_ti" class="form-label">Judul Tata Ibadah <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="judul_ti" name="judul"
                            placeholder="Masukkan judul Tata Ibadah" required aria-describedby="judul_ti-error">
                        <div class="invalid-feedback" id="judul_ti-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_terbit_ti" class="form-label">Tanggal Terbit <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_terbit_ti" name="tanggal_terbit" required
                            aria-describedby="tanggal_terbit_ti-error">
                        <div class="invalid-feedback" id="tanggal_terbit_ti-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="file_pdf_ti" class="form-label">File PDF Tata Ibadah (Max: 20MB) <span
                                id="file_pdf_ti_required_indicator" class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file_pdf_ti" name="file_pdf"
                            accept="application/pdf" aria-describedby="file_pdf_ti-error">
                        <div id="file_pdf_ti-error" class="invalid-feedback d-block"></div>
                        <small id="current-file-info-ti" class="form-text text-muted d-none mt-1">
                            File saat ini: <a href="#" id="current-file-link-ti" target="_blank"><span
                                    id="current-file-name-ti"></span></a>
                        </small>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="is_published_ti"
                            name="is_published" checked>
                        <label class="form-check-label" for="is_published_ti">
                            Publikasikan Tata Ibadah
                        </label>
                        <div class="invalid-feedback" id="is_published_ti-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-ti">Simpan</button>
            </div>
        </div>
    </div>
</div>
