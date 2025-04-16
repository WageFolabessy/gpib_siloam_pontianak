<button type="button" class="btn btn-primary btn-icon-split mb-4" id="btn-add-jadwal">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Jadwal Ibadah Baru</span>
</button>

<div class="modal fade" id="jadwalIbadahModal" tabindex="-1" aria-labelledby="jadwalIbadahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jadwalIbadahModalLabel">Form Jadwal Ibadah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form --}}
                <form id="jadwalIbadahForm" name="jadwalIbadahForm">
                    <input type="hidden" name="jadwal_ibadah_id" id="jadwal_ibadah_id">
                    <input type="hidden" name="_method" id="form_method">

                    <div class="alert alert-danger d-none" id="error-alert" role="alert"></div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan"
                            placeholder="Masukkan keterangan ibadah (Contoh: Ibadah Minggu Pagi)" required
                            aria-describedby="keterangan-error">
                        <div class="invalid-feedback" id="keterangan-error"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="hari" class="form-label">Hari (Opsional)</label>
                            <input type="text" class="form-control" id="hari" name="hari"
                                placeholder="Contoh: Minggu" aria-describedby="hari-error">
                            <div class="invalid-feedback" id="hari-error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="jam" class="form-label">Jam</label>
                            <input type="text" class="form-control" id="jam" name="jam"
                                placeholder="Contoh: 09:00" required aria-describedby="jam-error">
                            <div class="invalid-feedback" id="jam-error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kategori" class="form-label">Kategori Ibadah</label>
                            <select class="form-select" id="kategori" name="kategori" required
                                aria-label="Kategori Ibadah" aria-describedby="kategori-error">
                                <option value="" selected disabled>Pilih Kategori...</option>
                                <option value="Ibadah Minggu">Ibadah Minggu</option>
                                <option value="Ibadah Pelkat">Ibadah Pelkat</option>
                            </select>
                            <div class="invalid-feedback" id="kategori-error"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-jadwal">Simpan</button>
            </div>
        </div>
    </div>
</div>
