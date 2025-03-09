// Inisialisasi DataTable
$('#jadwalIbadahTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/dashboard/jadwal_ibadah/jadwal_ibadahTable",
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
        },
        {
            data: 'keterangan',
            name: 'keterangan'
        },
        {
            data: 'hari',
            name: 'hari'
        },
        {
            data: 'jam',
            name: 'jam'
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
    $('#tombol-simpan').off('click').on('click', tambahJadwalIbadah);
});

// Tombol Edit diklik
$(document).on('click', '#tombol-edit', function(e) {
    let id = $(this).data('id');

    $.ajax({
        url: '/dashboard/jadwal_ibadah/edit_jadwal_ibadah/' + id,
        type: 'GET',
        success: function(response) {
            let labelEditModal = document.getElementById('exampleModalLabel');
            let tombolUpdate = document.getElementById('tombol-simpan');
            labelEditModal.innerHTML = "Edit Jadwal Ibadah";
            tombolUpdate.innerHTML = "Perbaharui";
            $('#exampleModal').modal('show');
            $('#keterangan').val(response.data.keterangan);
            $('#hari').val(response.data.hari);
            $('#jam').val(response.data.jam);
            $('#kategori').val(response.data.kategori);

            // Setel event klik untuk operasi edit
            $('#tombol-simpan').off('click').on('click', function() {
                updateJadwalIbadah(id);
            });
        }
    });
});

// Tombol Hapus diklik
$(document).on('click', '#tombol-hapus', function(e) {
    let id = $(this).data('id');

    if (confirm('Apakah Anda yakin ingin menghapus jadwal ibadah ini?')) {
        $.ajax({
            url: '/dashboard/jadwal_ibadah/hapus_jadwal_ibadah/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#jadwalIbadahTable').DataTable().ajax.reload();
                alert('Jadwal Ibadah berhasil dihapus');
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat menghapus jadwal ibadah');
            }
        });
    }
});

// Fungsi untuk menambah renungan
function tambahJadwalIbadah() {
    clearErrors();

    let formData = new FormData();
    formData.append('keterangan', $('#keterangan').val());
    formData.append('hari', $('#hari').val());
    formData.append('jam', $('#jam').val());
    formData.append('kategori', $('#kategori').val());

    $.ajax({
        url: "/dashboard/jadwal_ibadah/simpan_jadwal_ibadah",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#jadwalIbadahTable').DataTable().ajax.reload();
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
function updateJadwalIbadah(id) {
    clearErrors();

    let formData = new FormData();
    formData.append('keterangan', $('#keterangan').val());
    formData.append('hari', $('#hari').val());
    formData.append('jam', $('#jam').val());
    formData.append('kategori', $('#kategori').val());

    $.ajax({
        url: '/dashboard/jadwal_ibadah/update_jadwal_ibadah/' + id,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert(response.message)
            $('#jadwalIbadahTable').DataTable().ajax.reload();
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
    $('#keterangan').val('');
    $('#hari').val('');
    $('#jam').val('');
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