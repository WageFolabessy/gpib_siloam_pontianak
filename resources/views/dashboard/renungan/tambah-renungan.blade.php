<div class="form-container">
    <div class="mb-3">
        <label for="judul" class="form-label">Judul Renungan</label>
        <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul renungan" />
        <span class="text-danger error-message" id="error-judul"></span>
    </div>
    <div class="mb-3">
        <label for="alkitab" class="form-label">Alkitab</label>
        <input type="text" class="form-control" id="alkitab" name="alkitab"
            placeholder="Alkitab" />
        <span class="text-danger error-message" id="error-alkitab"></span>
    </div>
    <div class="mb-3">
        <label for="bacaan_alkitab" class="form-label">Bacaan Alkitab</label>
        <input type="text" class="form-control" id="bacaan_alkitab" name="bacaan_alkitab"
            placeholder="Bacaan alkitab" />
        <span class="text-danger error-message" id="error-bacaan_alkitab"></span>
    </div>
    <div class="mb-3">
        <label for="thumbnail" class="form-label">Thumbnail</label>
        <input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/*" />
        <img src="" alt="thumbnail" class="img-fluid d-none" id="thumbnail-preview">
        <span class="text-danger error-message" id="error-thumbnail"></span>
    </div>
    <div class="mb-3">
        <label for="isi_bacaan" class="form-label">Isi Renungan</label>
        <textarea class="form-control" id="isi_bacaan" name="isi_bacaan" rows="5" placeholder="Isi renungan"></textarea>
        <span class="text-danger error-message" id="error-isi_bacaan"></span>
    </div>
</div>

