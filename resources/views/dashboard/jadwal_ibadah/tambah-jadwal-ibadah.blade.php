<div class="form-container">
    <div class="mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <input type="text" class="form-control" id="keterangan" name="keterangan"
            placeholder="Masukkan keterangan ibadah" />
        <span class="text-danger error-message" id="error-keterangan"></span>
    </div>
    <div class="mb-3">
        <label for="hari" class="form-label">Hari</label>
        <input type="text" class="form-control" id="hari" name="hari"
            placeholder="Masukkan hari ibadah" />
        <span class="text-danger error-message" id="error-hari"></span>
    </div>
    <div class="mb-3">
        <label for="jam" class="form-label">Jam</label>
        <input type="text" class="form-control" id="jam" name="jam"
            placeholder="Masukkan jam ibadah"/>
        <span class="text-danger error-message" id="error-jam"></span>
    </div>
    <div class="mb-3">
        <label for="kategori" class="form-label">Kategori Ibadah</label>
        <select class="form-select" aria-label="Kategori Ibadah" id="kategori">
            <option disabled selected>Pilih Kategori Ibadah...</option>
            <option value="Ibadah Minggu">Ibadah Minggu</option>
            <option value="Ibadah Pelkat">Ibadah Pelkat</option>
          </select>
        <span class="text-danger error-message" id="error-kategori"></span>
    </div>
</div>

