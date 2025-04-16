$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const renunganModalElement = document.getElementById("renunganModal");
    const renunganModal = renunganModalElement
        ? new bootstrap.Modal(renunganModalElement)
        : null;
    const modalTitle = $("#renunganModalLabel");
    const renunganForm = $("#renunganForm");
    const btnSave = $("#btn-save");
    const errorAlert = $("#error-alert");
    const thumbnailInput = $("#thumbnail");
    const thumbnailPreview = $("#thumbnail-preview");
    const currentThumbnailInfo = $("#current-thumbnail-info");
    const currentThumbnailName = $("#current-thumbnail-name");
    const isiBacaanTextarea = $("#isi_bacaan");

    const BASE_URL = "/dashboard/renungan";

    if ($.fn.summernote) {
        isiBacaanTextarea.summernote({
            placeholder: "Tulis isi renungan di sini...",
            tabsize: 2,
            height: 300,
            toolbar: [
                ["style", ["style"]],
                ["font", ["bold", "underline", "clear"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["table", ["table"]],
                ["insert", ["link"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
        });
    } else {
        console.error("Summernote is not loaded.");
    }

    let renunganTable;
    if ($.fn.DataTable) {
        renunganTable = $("#renunganTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${BASE_URL}/renunganTable`,
                error: function (xhr, error, thrown) {
                    console.error("DataTables error:", xhr.responseText);
                    showFeedback(
                        "Gagal memuat data tabel. Coba muat ulang halaman.",
                        "error"
                    );
                },
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                    width: "5%",
                },
                { data: "judul", name: "judul", width: "25%" },
                {
                    data: "alkitab",
                    name: "alkitab",
                    orderable: false,
                    width: "15%",
                },
                {
                    data: "bacaan_alkitab",
                    name: "bacaan_alkitab",
                    orderable: false,
                    width: "15%",
                },
                { data: "created_at", name: "created_at", width: "20%" },
                { data: "updated_at", name: "updated_at", width: "20%" },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    width: "15%",
                    className: "text-center",
                },
            ],
            order: [[3, "desc"]],
            drawCallback: function (settings) {
                initializeTooltips(this.api().table().container());
            },
            language: {
                processing: "Sedang memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ total entri)",
                loadingRecords: "Memuat...",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Berikutnya",
                    last: "Terakhir",
                },
                aria: {
                    sortAscending:
                        ": aktifkan untuk mengurutkan kolom secara ascending",
                    sortDescending:
                        ": aktifkan untuk mengurutkan kolom secara descending",
                },
            },
        });
    } else {
        console.error("DataTables is not loaded.");
    }

    $("#btn-add-renungan").click(function () {
        setFormState("add");
        if (renunganModal) renunganModal.show();
    });

    $("#renunganTable").on("click", ".tombol-edit", function () {
        const renunganId = $(this).data("id");
        if (!renunganId) return;

        setFormState("edit", renunganId);

        $.ajax({
            url: `${BASE_URL}/edit_renungan/${renunganId}`,
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                btnSave.prop("disabled", true);
            },
            success: function (response) {
                const data = response.data;
                
                if (!data) {
                    showFeedback("Data renungan tidak ditemukan.", "error");
                    return;
                }
                $("#renungan_id").val(data.id);
                $("#judul").val(data.judul);
                $("#alkitab").val(data.alkitab);
                $("#bacaan_alkitab").val(data.bacaan_alkitab);
                isiBacaanTextarea.summernote("code", data.isi_bacaan || "");

                if (data.thumbnail) {
                    thumbnailPreview
                        .attr("src", "/storage/" + data.thumbnail)
                        .removeClass("d-none");
                    currentThumbnailName.text(
                        data.thumbnail ? data.thumbnail.split("/").pop() : "N/A"
                    );
                    currentThumbnailInfo.removeClass("d-none");
                } else {
                    thumbnailPreview.addClass("d-none").attr("src", "#");
                    currentThumbnailInfo.addClass("d-none");
                }
                if (renunganModal) renunganModal.show();
            },
            error: function (xhr) {
                showFeedback(
                    "Gagal memuat data renungan untuk diedit.",
                    "error"
                );
                console.error(xhr.responseText);
            },
            complete: function () {
                btnSave.prop("disabled", false);
            },
        });
    });

    $("#renunganTable").on("click", ".tombol-hapus", function () {
        const renunganId = $(this).data("id");
        const renunganJudul = $(this).data("judul");
        if (!renunganId) return;

        if (
            confirm(
                `Apakah Anda yakin ingin menghapus renungan "${renunganJudul}"?`
            )
        ) {
            $.ajax({
                url: `${BASE_URL}/hapus_renungan/${renunganId}`,
                type: "DELETE",
                beforeSend: function () {},
                success: function (response) {
                    showFeedback(
                        response.message || "Renungan berhasil dihapus.",
                        "success"
                    );
                    if (renunganTable) renunganTable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    const errorMsg =
                        xhr.responseJSON?.message ||
                        "Terjadi kesalahan saat menghapus data.";
                    showFeedback(errorMsg, "error");
                    console.error(xhr.responseText);
                },
            });
        }
    });

    btnSave.click(function () {
        const renunganId = $("#renungan_id").val();
        const url = renunganId
            ? `${BASE_URL}/update_renungan/${renunganId}`
            : `${BASE_URL}/simpan_renungan`;
        const method = "POST";

        const isiBacaanContent = isiBacaanTextarea.summernote("code");

        const formData = new FormData(renunganForm[0]);
        formData.set("isi_bacaan", isiBacaanContent);

        if (renunganId) {
            formData.append("_method", "PUT");
        }

        clearErrors();
        $(this)
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
            );

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                showFeedback(
                    response.message || "Data berhasil disimpan.",
                    "success"
                );
                if (renunganModal) renunganModal.hide();
                if (renunganTable) renunganTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    displayValidationErrors(xhr.responseJSON.errors);
                } else {
                    const errorMsg =
                        xhr.responseJSON?.message ||
                        "Terjadi kesalahan server.";
                    errorAlert.text(errorMsg).removeClass("d-none");
                }
                console.error("Save/Update Error:", xhr.responseText);
            },
            complete: function () {
                btnSave
                    .prop("disabled", false)
                    .text(renunganId ? "Simpan Perubahan" : "Tambah");
            },
        });
    });

    if (renunganModalElement) {
        renunganModalElement.addEventListener(
            "hidden.bs.modal",
            function (event) {
                clearForm();
            }
        );
    }

    thumbnailInput.change(function () {
        const file = this.files[0];
        thumbnailPreview.addClass("d-none").attr("src", "#");
        $(this).removeClass("is-invalid");
        $("#thumbnail-error").text("");
        currentThumbnailInfo.addClass("d-none");

        if (file && file.type.startsWith("image/")) {
            const maxSize = 16 * 1024 * 1024;
            if (file.size > maxSize) {
                $(this).addClass("is-invalid").val("");
                $("#thumbnail-error").text(
                    `Ukuran file maks ${maxSize / 1024 / 1024} MB.`
                );
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                thumbnailPreview
                    .attr("src", e.target.result)
                    .removeClass("d-none");
            };
            reader.readAsDataURL(file);
        } else if (file) {
            $(this).addClass("is-invalid").val("");
            $("#thumbnail-error").text("File harus berupa gambar.");
        }
    });

    function setFormState(state, id = null) {
        clearForm();
        if (state === "add") {
            modalTitle.text("Tambah Renungan Baru");
            $("#form_method").val("POST");
            btnSave.text("Tambah");
        } else if (state === "edit") {
            modalTitle.text("Edit Renungan");
            $("#form_method").val("PUT");
            $("#renungan_id").val(id);
            btnSave.text("Simpan Perubahan");
        }
    }

    function clearForm() {
        renunganForm[0].reset();
        $("#renungan_id").val("");
        $("#form_method").val("");
        if ($.fn.summernote) {
            isiBacaanTextarea.summernote("code", "");
        }
        thumbnailPreview.addClass("d-none").attr("src", "#");
        currentThumbnailInfo.addClass("d-none");
        clearErrors();
    }

    function clearErrors() {
        renunganForm.find(".is-invalid").removeClass("is-invalid");
        renunganForm.find(".invalid-feedback").text("");
        errorAlert.addClass("d-none").text("");
    }

    function displayValidationErrors(errors) {
        clearErrors();
        let firstErrorField = null;
        errorAlert
            .text("Harap perbaiki kesalahan berikut:")
            .removeClass("d-none");

        for (const field in errors) {
            if (!firstErrorField) firstErrorField = field;
            const input = $(`#${field}`);
            const errorDiv = $(`#${field}-error`);

            if (input.length) {
                input.addClass("is-invalid");
                if (field === "isi_bacaan") {
                }
            } else if (field === "thumbnail") {
                thumbnailInput.addClass("is-invalid");
            }

            if (errorDiv.length) {
                errorDiv.text(errors[field][0]);
            } else {
                errorAlert.append(
                    `<div>- ${field.replace(/_/g, " ")}: ${
                        errors[field][0]
                    }</div>`
                );
                console.warn(`Error div not found for field: ${field}`);
            }
        }

        if (firstErrorField) {
            const errorInput = $(`#${firstErrorField}`);
            if (errorInput.length && errorInput.is(":visible")) {
                errorInput.focus();
            } else if (firstErrorField === "thumbnail") {
                thumbnailInput.focus();
            } else if (firstErrorField === "isi_bacaan") {
                isiBacaanTextarea.summernote("focus");
            }
        }
    }

    function showFeedback(message, type = "info") {
        console.log(`Feedback (${type}): ${message}`);
        alert(message);
    }

    function initializeTooltips(container) {
        $(container)
            .find('[data-bs-toggle="tooltip"]')
            .each(function () {
                var existingTooltip = bootstrap.Tooltip.getInstance(this);
                if (existingTooltip) {
                    existingTooltip.dispose();
                }
                new bootstrap.Tooltip(this);
            });
    }
});
