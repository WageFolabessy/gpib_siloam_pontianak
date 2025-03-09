<!-- Tombol trigger modal -->
<button type="button" class="btn btn-primary btn-icon-split mb-4" id="tombol-tambah" data-bs-toggle="modal"
    data-bs-target="#exampleModal">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Jadwal Ibadah Baru</span>
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Jadwal Ibadah</h5>
                <button type="button" class="close" id='tombol-tambah' data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('dashboard.jadwal_ibadah.tambah-jadwal-ibadah')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="tombol-simpan">Tambah</button>
            </div>
        </div>
    </div>
</div>
