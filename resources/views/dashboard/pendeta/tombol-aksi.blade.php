<div class="d-flex justify-content-center align-items-center">
    <!-- Ikon Detail -->
    <a href="{{ route('info') }}" class="btn btn-primary btn-sm mr-2"
        id="tombol-detail" data-bs-toggle="tooltip" title="Detail">
        <i class="fas fa-info-circle"></i>
    </a>

    <!-- Ikon Edit -->
    <a href="#" class="btn btn-warning btn-sm mr-2" data-id='{{ $data->id }}' id="tombol-edit" data-bs-toggle="tooltip"
        title="Edit">
        <i class="fas fa-edit"></i>
    </a>

    <!-- Ikon Hapus -->
    <a href="#" class="btn btn-danger btn-sm" data-id='{{ $data->id }}' id="tombol-hapus"
        data-bs-toggle="tooltip" title="Hapus">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>
