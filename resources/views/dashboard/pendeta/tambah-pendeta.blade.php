<div class="form-container">
    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama" name="nama"
            placeholder="Masukkan nama" />
        <span class="text-danger error-message" id="error-nama"></span>
    </div>
    <div class="mb-3">
        <label for="kategori" class="form-label">Kategori Pengurus</label>
        <select class="form-select" aria-label="Kategori Pengurus" id="kategori">
            <option disabled selected>Pilih Kategori Pengurus...</option>
            <option value="Ketua Majelis Jemaat">Ketua Majelis Jemaat</option>
            <option value="Pendeta Jemaat">Pendeta Jemaat</option>
          </select>
        <span class="text-danger error-message" id="error-kategori"></span>
    </div>
</div>

