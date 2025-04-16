<div class="d-flex justify-content-center align-items-center">
    <button type="button" class="btn btn-warning btn-sm me-1 tombol-edit-jadwal" data-id="{{ $jadwalIbadah->id }}"
        data-bs-toggle="tooltip" title="Edit Jadwal">
        <i class="fas fa-edit fa-fw"></i>
    </button>

    <button type="button" class="btn btn-danger btn-sm tombol-hapus-jadwal" data-id="{{ $jadwalIbadah->id }}"
        data-keterangan="{{ $jadwalIbadah->keterangan }}" data-bs-toggle="tooltip" title="Hapus Jadwal">
        <i class="fas fa-trash-alt fa-fw"></i>
    </button>
</div>
