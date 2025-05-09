<button type="button" class="btn btn-primary btn-icon-split mb-4" id="btn-add-warta">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Warta Baru</span>
</button>

<div class="modal fade" id="wartaModal" tabindex="-1" aria-labelledby="wartaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wartaModalLabel">Form Warta Jemaat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="wartaForm" name="wartaForm" enctype="multipart/form-data">
                    {{-- ID untuk update --}}
                    <input type="hidden" name="warta_id" id="warta_id">
                    {{-- Method spoofing untuk update (otomatis dihandle JS jika pakai FormData dan POST) --}}
                    {{-- <input type="hidden" name="_method" id="form_method_warta"> --}}

                    <div class="alert alert-danger d-none" id="error-alert-warta" role="alert"></div>

                    <div class="mb-3">
                        <label for="judul_warta" class="form-label">Judul Warta <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="judul_warta" name="judul"
                            placeholder="Masukkan judul warta" required aria-describedby="judul_warta-error">
                        <div class="invalid-feedback" id="judul_warta-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_terbit_warta" class="form-label">Tanggal Terbit <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_terbit_warta" name="tanggal_terbit"
                            required aria-describedby="tanggal_terbit_warta-error">
                        <div class="invalid-feedback" id="tanggal_terbit_warta-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="file_pdf_warta" class="form-label">File PDF Warta (Max: 20MB) <span
                                id="file_pdf_required_indicator" class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file_pdf_warta" name="file_pdf"
                            accept="application/pdf" aria-describedby="file_pdf_warta-error">
                        <div id="file_pdf_warta-error" class="invalid-feedback d-block"></div>
                        <small id="current-file-info-warta" class="form-text text-muted d-none mt-1">
                            File saat ini: <a href="#" id="current-file-link-warta" target="_blank"><span
                                    id="current-file-name-warta"></span></a>
                        </small>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="is_published_warta"
                            name="is_published" checked>
                        <label class="form-check-label" for="is_published_warta">
                            Publikasikan Warta
                        </label>
                        <div class="invalid-feedback" id="is_published_warta-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-warta">Simpan</button>
            </div>
        </div>
    </div>
</div>
