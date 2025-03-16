// Inisialisasi DataTable
$('#templateTanyaJawabTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/dashboard/tanya_jawab/tanya_jawabTable",
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
        },
        {
            data: 'pertanyaan',
            name: 'pertanyaan'
        },
        {
            data: 'jawaban',
            name: 'jawaban'
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
    $('#tombol-simpan').off('click').on('click', tambahTemplateTanyaJawab);
});

// Tombol Edit diklik
$(document).on('click', '#tombol-edit', function(e) {
    let id = $(this).data('id');

    $.ajax({
        url: '/dashboard/tanya_jawab/edit_tanya_jawab/' + id,
        type: 'GET',
        success: function(response) {
            let labelEditModal = document.getElementById('exampleModalLabel');
            let tombolUpdate = document.getElementById('tombol-simpan');
            labelEditModal.innerHTML = "Edit Template Tanya Jawab";
            tombolUpdate.innerHTML = "Perbaharui";
            $('#exampleModal').modal('show');
            $('#pertanyaan').val(response.data.pertanyaan);
            $('#jawaban').val(response.data.jawaban);

            // Setel event klik untuk operasi edit
            $('#tombol-simpan').off('click').on('click', function() {
                updateTemplateTanyaJawab(id);
            });
        }
    });
});

// Tombol Detail diklik
$(document).on('click', '#tombol-detail', function(e) {
    let id = $(this).data('id');

    $.ajax({
        url: '/dashboard/tanya_jawab/detail_tanya_jawab/' + id,
        type: 'GET',
        success: function(response) {
            $('#DetailTanyaJawab').modal('show');
            $('#pertanyaan_detail').val(response.data.pertanyaan);
            $('#jawaban_detail').val(response.data.jawaban);
        }
    });
});

// Tombol Hapus diklik
$(document).on('click', '#tombol-hapus', function(e) {
    let id = $(this).data('id');

    if (confirm('Apakah Anda yakin ingin menghapus template tanya jawab ini?')) {
        $.ajax({
            url: '/dashboard/tanya_jawab/hapus_tanya_jawab/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#templateTanyaJawabTable').DataTable().ajax.reload();
                alert('Template tanya jawab berhasil dihapus');
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat menghapus template tanya jawab');
            }
        });
    }
});

// Fungsi untuk menambah renungan
function tambahTemplateTanyaJawab() {
    clearErrors();

    let formData = new FormData();
    formData.append('pertanyaan', $('#pertanyaan').val());
    formData.append('jawaban', $('#jawaban').val());

    $.ajax({
        url: "/dashboard/tanya_jawab/simpan_tanya_jawab",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#templateTanyaJawabTable').DataTable().ajax.reload();
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
function updateTemplateTanyaJawab(id) {
    clearErrors();

    let formData = new FormData();
    formData.append('pertanyaan', $('#pertanyaan').val());
    formData.append('jawaban', $('#jawaban').val());

    $.ajax({
        url: '/dashboard/tanya_jawab/update_tanya_jawab/' + id,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert(response.message)
            $('#templateTanyaJawabTable').DataTable().ajax.reload();
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
    $('#pertanyaan').val('');
    $('#jawaban').val('');
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