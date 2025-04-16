<div class="d-flex justify-content-center align-items-center">
    <button type="button" class="btn btn-primary btn-sm me-1 tombol-detail-tanya-jawab"
        data-id="{{ $templateTanyaJawab->id }}" data-bs-toggle="tooltip" title="Lihat Detail">
        <i class="fas fa-eye fa-fw"></i>
    </button>

    <button type="button" class="btn btn-warning btn-sm me-1 tombol-edit-tanya-jawab"
        data-id="{{ $templateTanyaJawab->id }}" data-bs-toggle="tooltip" title="Edit Template">
        <i class="fas fa-edit fa-fw"></i>
    </button>

    <button type="button" class="btn btn-danger btn-sm tombol-hapus-tanya-jawab"
        data-id="{{ $templateTanyaJawab->id }}"
        data-pertanyaan="{{ \Illuminate\Support\Str::limit($templateTanyaJawab->pertanyaan, 30) }}"
        data-bs-toggle="tooltip" title="Hapus Template">
        <i class="fas fa-trash-alt fa-fw"></i>
    </button>
</div>
