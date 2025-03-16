<div class="modal fade" id="DetailTanyaJawab" tabindex="-1" aria-labelledby="DetailTanyaJawabLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DetailTanyaJawabLabel">Detail Template Tanya Jawab</h5>
                <button type="button" class="close" id='tombol-tambah' data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-container">
                    <div class="mb-3">
                        <label for="pertanyaan" class="form-label">Pertanyaan</label>
                        <textarea class="form-control" id="pertanyaan_detail" name="pertanyaan" rows="5" placeholder="Pertanyaan" readonly></textarea>
                        <span class="text-danger error-message" id="error-pertanyaan"></span>
                    </div>
                    <div class="mb-3">
                        <label for="jawaban" class="form-label">Jawaban</label>
                        <textarea class="form-control" id="jawaban_detail" name="jawaban" rows="5" placeholder="Jawaban" readonly></textarea>
                        <span class="text-danger error-message" id="error-jawaban"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
