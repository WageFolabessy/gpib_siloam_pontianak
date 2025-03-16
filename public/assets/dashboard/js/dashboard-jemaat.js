// Inisialisasi DataTable
$('#jemaatTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/dashboard/admin/jemaatTable",
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
        },
        {
            data: 'name',
            name: 'name'
        },
        {
            data: 'email',
            name: 'email'
        },
        {
            data: 'created_at',
            name: 'created_at'
        },
        {
            data: 'updated_at',
            name: 'updated_at'
        },
        {
            data: 'aksi',
            name: 'aksi',
        }
    ]
});

// Tombol Hapus diklik
$(document).on('click', '#tombol-hapus', function(e) {
    let id = $(this).data('id');

    if (confirm('Apakah Anda yakin ingin menghapus jemaat ini?')) {
        $.ajax({
            url: '/dashboard/admin/hapus_jemaat/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#jemaatTable').DataTable().ajax.reload();
                alert('Jemaat berhasil dihapus');
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat menghapus Jemaat');
            }
        });
    }
});