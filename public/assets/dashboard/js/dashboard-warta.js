$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const wartaModalElement = document.getElementById("wartaModal");
    const wartaModal = wartaModalElement
        ? new bootstrap.Modal(wartaModalElement)
        : null;
    const modalTitle = $("#wartaModalLabel");
    const wartaForm = $("#wartaForm");
    const btnSave = $("#btn-save-warta");
    const errorAlert = $("#error-alert-warta");

    const filePdfInput = $("#file_pdf_warta");
    const currentFileInfo = $("#current-file-info-warta");
    const currentFileLink = $("#current-file-link-warta");
    const currentFileName = $("#current-file-name-warta");
    const filePdfRequiredIndicator = $("#file_pdf_required_indicator");

    const BASE_URL = "/dashboard/warta_jemaat";

    let wartaTable;
    if ($.fn.DataTable) {
        wartaTable = $("#wartaJemaatTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${BASE_URL}/wartaJemaatTable`,
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
                    className: "text-center",
                },
                { data: "judul", name: "judul", width: "30%" },
                {
                    data: "tanggal_terbit",
                    name: "tanggal_terbit",
                    width: "20%",
                    className: "text-center",
                },
                {
                    data: "file_info",
                    name: "file_info",
                    orderable: false,
                    searchable: false,
                    width: "15%",
                },
                {
                    data: "status_publish",
                    name: "is_published",
                    width: "10%",
                    className: "text-center",
                },
                {
                    data: "updated_at",
                    name: "updated_at",
                    width: "15%",
                    className: "text-center",
                },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    width: "10%",
                    className: "text-center",
                },
            ],
            order: [[2, "desc"]],
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

    $("#btn-add-warta").click(function () {
        setFormState("add");
        if (wartaModal) wartaModal.show();
    });

    $("body").on("click", ".btn-edit-warta", function () {
        const wartaId = $(this).data("id");
        if (!wartaId) return;

        setFormState("edit", wartaId);

        $.ajax({
            url: `${BASE_URL}/edit_warta_jemaat/${wartaId}`,
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
                    showFeedback("Data warta tidak ditemukan.", "error");
                    return;
                }
                $("#warta_id").val(data.id);
                $("#judul_warta").val(data.judul);
                $("#tanggal_terbit_warta").val(
                    data.tanggal_terbit
                        ? moment(data.tanggal_terbit).format("YYYY-MM-DD")
                        : ""
                );
                $("#is_published_warta").prop("checked", data.is_published);

                if (data.current_file_name && data.current_file_url) {
                    currentFileName.text(data.current_file_name);
                    currentFileLink
                        .attr("href", data.current_file_url)
                        .removeClass("d-none");
                    currentFileInfo.removeClass("d-none");
                } else {
                    currentFileLink.addClass("d-none");
                    currentFileInfo.addClass("d-none");
                }
                if (wartaModal) wartaModal.show();
            },
            error: function (xhr) {
                showFeedback(
                    "Gagal memuat data warta untuk diedit. " +
                        (xhr.responseJSON?.message || ""),
                    "error"
                );
                console.error(xhr.responseText);
            },
            complete: function () {
                btnSave.prop("disabled", false).text("Simpan Perubahan");
            },
        });
    });

    $("body").on("click", ".btn-delete-warta", function () {
        const wartaId = $(this).data("id");
        const wartaJudul = $(this).data("judul");
        if (!wartaId) return;

        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Apakah Anda yakin?",
                html: `Anda akan menghapus warta: <br><strong>"${wartaJudul}"</strong>`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete(wartaId);
                }
            });
        } else {
            if (
                confirm(
                    `Apakah Anda yakin ingin menghapus warta "${wartaJudul}"?`
                )
            ) {
                performDelete(wartaId);
            }
        }
    });

    function performDelete(wartaId) {
        $.ajax({
            url: `${BASE_URL}/hapus_warta_jemaat/${wartaId}`,
            type: "DELETE",
            success: function (response) {
                showFeedback(
                    response.message || "Warta berhasil dihapus.",
                    "success",
                    true
                );
                if (wartaTable) wartaTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                const errorMsg =
                    xhr.responseJSON?.message ||
                    "Terjadi kesalahan saat menghapus data.";
                showFeedback(errorMsg, "error", true);
                console.error(xhr.responseText);
            },
        });
    }

    btnSave.click(function () {
        const wartaId = $("#warta_id").val();
        const url = wartaId
            ? `${BASE_URL}/update_warta_jemaat/${wartaId}`
            : `${BASE_URL}/simpan_warta_jemaat`;
        const method = "POST";

        const formData = new FormData(wartaForm[0]);

        if (wartaId) {
            formData.append("_method", "PUT");
        }
        if (!$("#is_published_warta").is(":checked")) {
            formData.set("is_published", "0");
        } else {
            formData.set("is_published", "1");
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
                    "success",
                    true
                );
                if (wartaModal) wartaModal.hide();
                if (wartaTable) wartaTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    displayValidationErrors(xhr.responseJSON.errors);
                } else {
                    const errorMsg =
                        xhr.responseJSON?.message ||
                        "Terjadi kesalahan server.";
                    errorAlert.html(errorMsg).removeClass("d-none");
                    showFeedback(errorMsg, "error");
                }
                console.error("Save/Update Error:", xhr.responseText);
            },
            complete: function () {
                btnSave
                    .prop("disabled", false)
                    .text(wartaId ? "Simpan Perubahan" : "Tambah");
            },
        });
    });

    if (wartaModalElement) {
        wartaModalElement.addEventListener("hidden.bs.modal", function (event) {
            clearForm();
        });
    }

    filePdfInput.change(function () {
        const file = this.files[0];
        $(this).removeClass("is-invalid");
        $("#file_pdf_warta-error").text("");
        currentFileInfo.addClass("d-none");

        if (file) {
            if (file.type === "application/pdf") {
                const maxSize = 20 * 1024 * 1024;
                if (file.size > maxSize) {
                    $(this).addClass("is-invalid").val("");
                    $("#file_pdf_warta-error").text(
                        `Ukuran file maks ${maxSize / 1024 / 1024} MB.`
                    );
                    return;
                }
                currentFileName.text(file.name);
                currentFileLink.removeAttr("href").addClass("d-none");
                currentFileInfo.removeClass("d-none");
            } else {
                $(this).addClass("is-invalid").val("");
                $("#file_pdf_warta-error").text("File harus berformat PDF.");
            }
        }
    });

    function setFormState(state, id = null) {
        clearForm();
        if (state === "add") {
            modalTitle.text("Tambah Warta Jemaat Baru");
            btnSave.text("Tambah");
            filePdfInput.attr("required", true);
            filePdfRequiredIndicator.removeClass("d-none");
        } else if (state === "edit") {
            modalTitle.text("Edit Warta Jemaat");
            $("#warta_id").val(id);
            btnSave.text("Simpan Perubahan");
            filePdfInput.removeAttr("required");
            filePdfRequiredIndicator.addClass("d-none");
        }
    }

    function clearForm() {
        wartaForm[0].reset();
        $("#warta_id").val("");
        currentFileInfo.addClass("d-none");
        currentFileLink.removeAttr("href").addClass("d-none");
        $("#is_published_warta").prop("checked", true);
        clearErrors();
        filePdfInput.attr("required", true);
        filePdfRequiredIndicator.removeClass("d-none");
    }

    function clearErrors() {
        wartaForm.find(".is-invalid").removeClass("is-invalid");
        wartaForm.find(".invalid-feedback").text("");
        errorAlert.addClass("d-none").html("");
    }

    function displayValidationErrors(errors) {
        clearErrors();
        let firstErrorField = null;
        let errorSummaryHtml = "<ul>";

        for (const field in errors) {
            if (!firstErrorField) firstErrorField = field;

            const inputElement = $(`#${field}_warta`);
            const errorDivElement = $(`#${field}_warta-error`);

            let targetInput = inputElement;
            let targetErrorDiv = errorDivElement;

            if (field === "file_pdf") {
                targetInput = filePdfInput;
                targetErrorDiv = $("#file_pdf_warta-error");
            } else if (field === "judul") {
                targetInput = $("#judul_warta");
                targetErrorDiv = $("#judul_warta-error");
            } else if (field === "tanggal_terbit") {
                targetInput = $("#tanggal_terbit_warta");
                targetErrorDiv = $("#tanggal_terbit_warta-error");
            } else if (field === "is_published") {
                targetInput = $("#is_published_warta");
                targetErrorDiv = $("#is_published_warta-error");
            }

            if (targetInput.length) {
                targetInput.addClass("is-invalid");
            }
            if (targetErrorDiv.length) {
                targetErrorDiv.text(errors[field][0]);
            }
            errorSummaryHtml += `<li>${errors[field][0]}</li>`;
        }
        errorSummaryHtml += "</ul>";
        errorAlert
            .html(
                "<strong>Harap perbaiki kesalahan berikut:</strong>" +
                    errorSummaryHtml
            )
            .removeClass("d-none");

        if (firstErrorField) {
            const errorInputToFocus = $(`#${firstErrorField}_warta`);
            if (errorInputToFocus.length && errorInputToFocus.is(":visible")) {
                errorInputToFocus.focus();
            } else if (firstErrorField === "file_pdf") {
                filePdfInput.focus();
            }
        }
    }

    function showFeedback(message, type = "info", useSwal = false) {
        console.log(`Feedback (${type}): ${message}`);
        if (useSwal && typeof Swal !== "undefined") {
            Swal.fire({
                icon: type,
                title: type.charAt(0).toUpperCase() + type.slice(1) + "!",
                text: message,
                timer: type === "success" ? 2000 : 3000,
                showConfirmButton: type !== "success",
            });
        } else {
            alert(message);
        }
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
