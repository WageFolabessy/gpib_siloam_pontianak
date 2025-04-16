{{-- Tombol Trigger Modal Tambah --}}
<button type="button" class="btn btn-primary btn-icon-split mb-4" id="btn-add-pendeta">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Tambah Pendeta/Majelis</span>
</button>

{{-- Modal untuk Tambah/Edit Pendeta --}}
<div class="modal fade" id="pendetaModal" tabindex="-1" aria-labelledby="pendetaModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendetaModalLabel">Form Pendeta/Majelis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form --}}
                <form id="pendetaForm" name="pendetaForm">
                    <input type="hidden" name="pendeta_id" id="pendeta_id">
                    <input type="hidden" name="_method" id="form_method">

                    <div class="alert alert-danger d-none" id="error-alert" role="alert"></div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                            placeholder="Masukkan nama lengkap" required aria-describedby="nama-error">
                        <div class="invalid-feedback" id="nama-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori" required
                            aria-label="Kategori Pengurus" aria-describedby="kategori-error">
                            <option value="" selected disabled>Pilih Kategori...</option>
                            <option value="Ketua Majelis Jemaat">Ketua Majelis Jemaat</option>
                            <option value="Pendeta Jemaat">Pendeta Jemaat</option>
                        </select>
                        <div class="invalid-feedback" id="kategori-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-pendeta">Simpan</button>

            </div>
        </div>
    </div>
</div>
