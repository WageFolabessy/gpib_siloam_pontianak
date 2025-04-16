{{-- Tombol Trigger Modal Tambah --}}
<button type="button" class="btn btn-primary btn-icon-split mb-4" id="btn-add-tanya-jawab">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Template Tanya Jawab Baru</span>
</button>

{{-- Modal untuk Tambah/Edit Template Tanya Jawab --}}
<div class="modal fade" id="tanyaJawabModal" tabindex="-1" aria-labelledby="tanyaJawabModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tanyaJawabModalLabel">Form Template Tanya Jawab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form --}}
                <form id="tanyaJawabForm" name="tanyaJawabForm">
                    <input type="hidden" name="template_tanya_jawab_id" id="template_tanya_jawab_id">
                    <input type="hidden" name="_method" id="form_method">

                    <div class="alert alert-danger d-none" id="error-alert" role="alert"></div>

                    <div class="mb-3">
                        <label for="pertanyaan" class="form-label">Pertanyaan</label>
                        <textarea class="form-control" id="pertanyaan" name="pertanyaan" rows="4" placeholder="Masukkan pertanyaan..."
                            required aria-describedby="pertanyaan-error"></textarea> 
                        <div class="invalid-feedback" id="pertanyaan-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="jawaban" class="form-label">Jawaban</label>
                        <textarea class="form-control" id="jawaban" name="jawaban" rows="8" placeholder="Masukkan jawaban..." required
                            aria-describedby="jawaban-error"></textarea>
                        <div class="invalid-feedback" id="jawaban-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-tanya-jawab">Simpan</button>
            </div>
        </div>
    </div>
</div>
