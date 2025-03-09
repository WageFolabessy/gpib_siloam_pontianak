// Inisialisasi DataTable
$('#adminTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/dashboard/admin/adminTable",
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
        },
        {
            data: 'username',
            name: 'username'
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
    $('#tombol-simpan').off('click').on('click', tambahAdmin);
});

// Tombol Edit diklik
$(document).on('click', '#tombol-edit', function(e) {
    let id = $(this).data('id');

    $.ajax({
        url: '/dashboard/admin/edit_admin/' + id,
        type: 'GET',
        success: function(response) {
            let labelEditModal = document.getElementById('exampleModalLabel');
            labelEditModal.innerHTML = "Edit Admin";
            document.getElementsByName('password')[0].placeholder = 'Masukkan password baru';
            $('#exampleModal').modal('show');
            $('#username').val(response.data.username);

            // Setel event klik untuk operasi edit
            $('#tombol-simpan').off('click').on('click', function() {
                updateAdmin(id);
            });
        }
    });
});

// Tombol Hapus diklik
$(document).on('click', '#tombol-hapus', function(e) {
    let id = $(this).data('id');

    if (confirm('Apakah Anda yakin ingin menghapus admin ini?')) {
        $.ajax({
            url: '/dashboard/admin/hapus_admin/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#adminTable').DataTable().ajax.reload();
                alert('Admin berhasil dihapus');
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat menghapus Admin');
            }
        });
    }
});

// Fungsi untuk menambah renungan
function tambahAdmin() {
    clearErrors();

    let formData = new FormData();
    formData.append('username', $('#username').val());
    formData.append('password', $('#password').val());

    $.ajax({
        url: "/dashboard/admin/simpan_admin",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#adminTable').DataTable().ajax.reload();
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
function updateAdmin(id) {
    clearErrors();

    let formData = new FormData();
    formData.append('username', $('#username').val());
    let password = $('#password').val();
    if (password) {
        formData.append('password', password);
    }

    $.ajax({
        url: '/dashboard/admin/update_admin/' + id,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert(response.message)
            $('#adminTable').DataTable().ajax.reload();
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
    $('#username').val('');
    $('#password').val('');
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