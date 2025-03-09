// Inisialisasi DataTable
$('#pendetaTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/dashboard/pendeta/pendetaTable",
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
        },
        {
            data: 'nama',
            name: 'nama'
        },
        {
            data: 'kategori',
            name: 'kategori'
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

// Tombol Tambah diklik
$('#tombol-tambah').click(function() {
    clearForm();

    // Setel event klik untuk operasi tambah
    $('#tombol-simpan').off('click').on('click', tambahPendeta);
});

// Tombol Edit diklik
$(document).on('click', '#tombol-edit', function(e) {
    let id = $(this).data('id');

    $.ajax({
        url: '/dashboard/pendeta/edit_pendeta/' + id,
        type: 'GET',
        success: function(response) {
            let labelEditModal = document.getElementById('exampleModalLabel');
            labelEditModal.innerHTML = "Edit Pengurus";
            $('#exampleModal').modal('show');
            $('#nama').val(response.data.nama);
            $('#kategori').val(response.data.kategori);

            // Setel event klik untuk operasi edit
            $('#tombol-simpan').off('click').on('click', function() {
                updatePendeta(id);
            });
        }
    });
});

// Tombol Hapus diklik
$(document).on('click', '#tombol-hapus', function(e) {
    let id = $(this).data('id');

    if (confirm('Apakah Anda yakin ingin menghapus pengurus ini?')) {
        $.ajax({
            url: '/dashboard/pendeta/hapus_pendeta/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#pendetaTable').DataTable().ajax.reload();
                alert('Pengurus berhasil dihapus');
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat menghapus Pengurus');
            }
        });
    }
});

// Fungsi untuk menambah renungan
function tambahPendeta() {
    clearErrors();

    let formData = new FormData();
    formData.append('nama', $('#nama').val());
    formData.append('kategori', $('#kategori').val());

    $.ajax({
        url: "/dashboard/pendeta/simpan_pendeta",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#pendetaTable').DataTable().ajax.reload();
            $('#exampleModal').modal('hide');
        },
        error: function(xhr, status, error) {
            let errors = xhr.responseJSON.errors;
            for (let field in errors) {
                let errorMessage = errors[field][
                    0
                ]; // Ambil pesan error pertama untuk setiap field
                // Tampilkan pesan error di field yang relevan
                $(`#${field}`).siblings('.error-message').text(errorMessage);
            }
        }
    }).fail(function() {
        alert('Terjadi kesalahan saat mengirim data ke server.');
    });
}

// Fungsi untuk memperbarui renungan
function updatePendeta(id) {
    clearErrors();

    let formData = new FormData();
    formData.append('nama', $('#nama').val());
    formData.append('kategori', $('#kategori').val());

    $.ajax({
        url: '/dashboard/pendeta/update_pendeta/' + id,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert(response.message)
            $('#pendetaTable').DataTable().ajax.reload();
            $('#exampleModal').modal('hide');
        },
        error: function(xhr, status, error) {
            let errors = xhr.responseJSON.errors;
            for (let field in errors) {
                let errorMessage = errors[field][
                0]; // Ambil pesan error pertama untuk setiap field
                // Tampilkan pesan error di field yang relevan
                $(`#${field}`).siblings('.error-message').text(errorMessage);
            }
        }
    }).fail(function() {
        alert('Terjadi kesalahan saat mengirim data ke server.');
    });
}

// Fungsi untuk membersihkan form
function clearForm() {
    $('#nama').val('');
    let kategori = document.getElementById('kategori');
    kategori.selectedIndex = 0;
}

// Ketika modal ditutup, bersihkan form
$('#exampleModal').on('hidden.bs.modal', function() {
    clearForm();
    clearErrors();
});

// Fungsi untuk menghapus pesan error
function clearErrors() {
    $('.error-message').text('');
}