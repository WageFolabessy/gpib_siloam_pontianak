{{-- File: resources/views/dashboard/renungan/tombol-aksi.blade.php --}}

<div class="d-flex justify-content-center align-items-center">
    {{-- Tombol Edit: Trigger JS untuk modal edit --}}
    <button type="button"
            class="btn btn-warning btn-sm me-1 tombol-edit"
            data-id="{{ $renungan->id }}"
            data-bs-toggle="tooltip"
            title="Edit Renungan">
        <i class="fas fa-edit fa-fw"></i>
    </button>

    {{-- Tombol Hapus: Trigger JS untuk konfirmasi & hapus AJAX --}}
    <button type="button"
            class="btn btn-danger btn-sm tombol-hapus"
            data-id="{{ $renungan->id }}"
            data-judul="{{ $renungan->judul }}"
            data-bs-toggle="tooltip"
            title="Hapus Renungan">
        <i class="fas fa-trash-alt fa-fw"></i>
    </button>
</div>