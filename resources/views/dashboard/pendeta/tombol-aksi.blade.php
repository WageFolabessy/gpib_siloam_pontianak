<div class="d-flex justify-content-center align-items-center">
    <button type="button" class="btn btn-warning btn-sm me-1 tombol-edit-pendeta" data-id="{{ $pendeta->id }}"
        data-bs-toggle="tooltip" title="Edit Data">
        <i class="fas fa-edit fa-fw"></i>
    </button>

    <button type="button" class="btn btn-danger btn-sm tombol-hapus-pendeta" data-id="{{ $pendeta->id }}"
        data-nama="{{ $pendeta->nama }}" data-bs-toggle="tooltip" title="Hapus Data">
        <i class="fas fa-trash-alt fa-fw"></i>
    </button>
</div>
