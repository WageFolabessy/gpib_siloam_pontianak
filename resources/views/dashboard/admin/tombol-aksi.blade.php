<div class="d-flex justify-content-center align-items-center">
    <button type="button" class="btn btn-warning btn-sm me-1 tombol-edit-admin" data-id="{{ $admin->id }}"
        data-bs-toggle="tooltip" title="Edit Admin">
        <i class="fas fa-edit fa-fw"></i>
    </button>

    <button type="button" class="btn btn-danger btn-sm tombol-hapus-admin" data-id="{{ $admin->id }}"
        data-username="{{ $admin->username }}" data-bs-toggle="tooltip" title="Hapus Admin">
        <i class="fas fa-user-times fa-fw"></i>
    </button>
</div>
