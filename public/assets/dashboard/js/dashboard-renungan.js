$(document).ready(function() {
    let thumbnail_preview = $('#thumbnail-preview')[0];

    // Inisialisasi TinyMCE
    tinymce.init({
        selector: 'textarea#isi_bacaan',
        passive: true
    });

    // Inisialisasi DataTable
    $('#renunganTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/dashboard/renungan/renunganTable",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'judul',
                name: 'judul'
            },
            {
                data: 'alkitab',
                name: 'alkitab'
            },
            {
                data: 'bacaan_alkitab',
                name: 'bacaan_alkitab'
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
                name: 'aksi'
            }
        ]
    });

    // Tombol Tambah diklik
    $('#tombol-tambah').click(function() {
        clearForm();

        // Setel event klik untuk operasi tambah
        $('#tombol-simpan').off('click').on('click', tambahRenungan);
    });

    // Tombol Edit diklik
    $(document).on('click', '#tombol-edit', function(e) {
        let id = $(this).data('id');

        $.ajax({
            url: '/dashboard/renungan/edit_renungan/' + id,
            type: 'GET',
            success: function(response) {
                let labelEditModal = document.getElementById('exampleModalLabel');
                labelEditModal.innerHTML = "Edit Renungan";
                $('#exampleModal').modal('show');
                $('#judul').val(response.data.judul);
                $('#alkitab').val(response.data.alkitab);
                $('#bacaan_alkitab').val(response.data.bacaan_alkitab);
                // Menampilkan thumbnail jika tersedia
                if (response.data.thumbnail) {
                    thumbnail_preview.classList.remove('d-none');
                    $('#thumbnail-preview').attr('src', '/storage/thumbnails/' +
                        response.data.thumbnail);
                }
                tinymce.get('isi_bacaan').setContent(response.data.isi_bacaan);

                // Setel event klik untuk operasi edit
                $('#tombol-simpan').off('click').on('click', function() {
                    updateRenungan(id);
                });
            }
        });
    });

    // Tombol Hapus diklik
    $(document).on('click', '#tombol-hapus', function(e) {
        let id = $(this).data('id');

        if (confirm('Apakah Anda yakin ingin menghapus renungan ini?')) {
            $.ajax({
                url: '/dashboard/renungan/hapus_renungan/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#renunganTable').DataTable().ajax.reload();
                    alert('Renungan berhasil dihapus');
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat menghapus renungan');
                }
            });
        }
    });

    // Fungsi untuk menambah renungan
    function tambahRenungan() {
        clearErrors();

        let formData = new FormData();
        formData.append('judul', $('#judul').val());
        formData.append('alkitab', $('#alkitab').val());
        formData.append('bacaan_alkitab', $('#bacaan_alkitab').val());
        // Cek apakah thumbnail ada atau tidak
        const thumbnailInput = $('#thumbnail')[0];
        if (thumbnailInput.files.length > 0) {
            formData.append('thumbnail', thumbnailInput.files[0]);
        }
        formData.append('isi_bacaan', tinymce.get('isi_bacaan').getContent());

        $.ajax({
            url: "/dashboard/renungan/simpan_renungan",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert(response.message)
                $('#renunganTable').DataTable().ajax.reload();
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
    function updateRenungan(id) {
        clearErrors();

        let formData = new FormData();
        
        formData.append('judul', $('#judul').val());
        formData.append('alkitab', $('#alkitab').val());
        formData.append('bacaan_alkitab', $('#bacaan_alkitab').val());
        // Cek apakah thumbnail ada atau tidak
        const thumbnailInput = $('#thumbnail')[0];
        if (thumbnailInput.files.length > 0) {
            formData.append('thumbnail', thumbnailInput.files[0]);
        }
        formData.append('isi_bacaan', tinymce.get('isi_bacaan').getContent());

        $.ajax({
            url: '/dashboard/renungan/update_renungan/' + id,
            type: 'POST', // Catatan: Gunakan POST dengan field "_method" diatur sebagai "PUT"
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#renunganTable').DataTable().ajax.reload();
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
        $('#judul').val('');
        $('#alkitab').val('');
        $('#bacaan_alkitab').val('');
        $('#thumbnail').val(''); // Reset input file
        $('#thumbnail-preview').attr('src', ''); // Reset tampilan thumbnail
        tinymce.get('isi_bacaan').setContent('');
    }

    // Ketika modal ditutup, bersihkan form
    $('#exampleModal').on('hidden.bs.modal', function() {
        clearForm();
        clearErrors();
        const thumbnail_preview = $('#thumbnail-preview')[0];
        thumbnail_preview.classList.add('d-none');
    });

    // Fungsi untuk menghapus pesan error
    function clearErrors() {
        $('.error-message').text('');
    }

});