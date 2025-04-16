$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const pendetaModalElement = document.getElementById("pendetaModal");
    const pendetaModal = pendetaModalElement
        ? new bootstrap.Modal(pendetaModalElement)
        : null;
    const modalTitle = $("#pendetaModalLabel");
    const pendetaForm = $("#pendetaForm");
    const btnSave = $("#btn-save-pendeta");
    const errorAlert = $("#error-alert");

    const BASE_URL = "/dashboard/pendeta";

    let pendetaTable;
    if ($.fn.DataTable) {
        pendetaTable = $("#pendetaTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${BASE_URL}/pendetaTable`,
                error: function (xhr, error, thrown) {
                    console.error("DataTables Error:", xhr.responseText);
                    showFeedback(
                        "Gagal memuat data tabel. Silakan muat ulang halaman.",
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
                { data: "nama", name: "nama", width: "40%" },
                { data: "kategori", name: "kategori", width: "25%" },
                { data: "created_at", name: "created_at", width: "10%" },
                { data: "updated_at", name: "updated_at", width: "10%" },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    width: "10%",
                    className: "text-center",
                },
            ],
            order: [[1, "asc"]],
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
            },
        });
    } else {
        console.error("DataTables library is not loaded.");
    }

    $("#btn-add-pendeta").click(function () {
        setFormState("add");
        if (pendetaModal) pendetaModal.show();
    });

    $("#pendetaTable").on("click", ".tombol-edit-pendeta", function () {
        const pendetaId = $(this).data("id");
        if (!pendetaId) return;

        setFormState("edit", pendetaId);

        $.ajax({
            url: `${BASE_URL}/edit_pendeta/${pendetaId}`,
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                btnSave
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...'
                    );
            },
            success: function (response) {
                const data = response.data;
                if (!data) {
                    showFeedback("Data Pengurus tidak ditemukan.", "error");
                    return;
                }
                $("#pendeta_id").val(data.id);
                $("#nama").val(data.nama);
                $("#kategori").val(data.kategori);

                if (pendetaModal) pendetaModal.show();
            },
            error: function (xhr) {
                showFeedback(
                    "Gagal memuat data Pengurus untuk diedit.",
                    "error"
                );
                console.error("Edit AJAX Error:", xhr.responseText);
            },
            complete: function () {
                btnSave.prop("disabled", false).text("Simpan Perubahan"); // Teks sesuai mode edit
            },
        });
    });

    $("#pendetaTable").on("click", ".tombol-hapus-pendeta", function () {
        const pendetaId = $(this).data("id");
        const pendetaNama = $(this).data("nama") || "ini";
        if (!pendetaId) return;

        if (
            confirm(`Apakah Anda yakin ingin menghapus data "${pendetaNama}"?`)
        ) {
            $.ajax({
                url: `${BASE_URL}/hapus_pendeta/${pendetaId}`,
                type: "DELETE",
                success: function (response) {
                    showFeedback(
                        response.message || "Data Pengurus berhasil dihapus.",
                        "success"
                    );
                    if (pendetaTable) pendetaTable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    const errorMsg =
                        xhr.responseJSON?.message ||
                        "Terjadi kesalahan saat menghapus data.";
                    showFeedback(errorMsg, "error");
                    console.error("Delete AJAX Error:", xhr.responseText);
                },
            });
        }
    });

    btnSave.click(function () {
        const pendetaId = $("#pendeta_id").val();
        const url = pendetaId
            ? `${BASE_URL}/update_pendeta/${pendetaId}`
            : `${BASE_URL}/simpan_pendeta`;
        const method = "POST";

        const formData = new FormData(pendetaForm[0]);
        if (pendetaId) {
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
                if (pendetaModal) pendetaModal.hide();
                if (pendetaTable) pendetaTable.ajax.reload(null, false);
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
                console.error("Save/Update AJAX Error:", xhr.responseText);
            },
            complete: function () {
                btnSave
                    .prop("disabled", false)
                    .text(pendetaId ? "Simpan Perubahan" : "Tambah");
            },
        });
    });

    if (pendetaModalElement) {
        pendetaModalElement.addEventListener(
            "hidden.bs.modal",
            function (event) {
                clearForm();
            }
        );
    }

    function setFormState(state, id = null) {
        clearForm();
        if (state === "add") {
            modalTitle.text("Tambah Data Pendeta/Majelis");
            $("#form_method").val("POST");
            $("#pendeta_id").val("");
            btnSave.text("Tambah");
        } else if (state === "edit") {
            modalTitle.text("Edit Data Pendeta/Majelis");
            $("#form_method").val("PUT");
            $("#pendeta_id").val(id);
            btnSave.text("Simpan Perubahan");
        }
    }

    function clearForm() {
        pendetaForm[0].reset();
        $("#pendeta_id").val("");
        $("#form_method").val("");
        clearErrors();
    }

    function clearErrors() {
        pendetaForm.find(".is-invalid").removeClass("is-invalid");
        pendetaForm.find(".invalid-feedback").text("");
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
            const inputElement = $(`#${field}`);
            const errorDiv = $(`#${field}-error`);

            if (inputElement.length) {
                inputElement.addClass("is-invalid");
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
            const firstErrorElement = $(`#${firstErrorField}`);
            if (firstErrorElement.length && firstErrorElement.is(":visible")) {
                firstErrorElement.focus();
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
