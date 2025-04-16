$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const adminModalElement = document.getElementById("adminModal");
    const adminModal = adminModalElement
        ? new bootstrap.Modal(adminModalElement)
        : null;
    const modalTitle = $("#adminModalLabel");
    const adminForm = $("#adminForm");
    const btnSave = $("#btn-save-admin");
    const errorAlert = $("#error-alert");
    const passwordInput = $("#password");
    const passwordConfirmationInput = $("#password_confirmation");

    const BASE_URL = "/dashboard/admin";

    let adminTable;
    if ($.fn.DataTable) {
        adminTable = $("#adminTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${BASE_URL}/adminTable`,
                error: function (xhr, error, thrown) {
                    console.error("DataTables Error:", xhr.responseText);
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
                { data: "username", name: "username", width: "45%" },
                { data: "created_at", name: "created_at", width: "15%" },
                { data: "updated_at", name: "updated_at", width: "15%" },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    width: "20%",
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

    $("#btn-add-admin").click(function () {
        setFormState("add");
        if (adminModal) adminModal.show();
    });
    $("#btn-add-admin-header").click(function () {
        $("#btn-add-admin").click();
    });

    $("#adminTable").on("click", ".tombol-edit-admin", function () {
        const adminId = $(this).data("id");
        if (!adminId) return;

        setFormState("edit", adminId);

        $.ajax({
            url: `${BASE_URL}/edit_admin/${adminId}`,
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
                    showFeedback("Data Admin tidak ditemukan.", "error");
                    return;
                }
                $("#admin_id").val(data.id);
                $("#username").val(data.username);
                passwordInput.val("");
                passwordConfirmationInput.val("");

                if (adminModal) adminModal.show();
            },
            error: function (xhr) {
                showFeedback("Gagal memuat data Admin untuk diedit.", "error");
                console.error("Edit AJAX Error:", xhr.responseText);
            },
            complete: function () {
                btnSave.prop("disabled", false).text("Simpan Perubahan");
            },
        });
    });

    $("#adminTable").on("click", ".tombol-hapus-admin", function () {
        const adminId = $(this).data("id");
        const adminUsername = $(this).data("username") || "ini";
        if (!adminId) return;

        if (
            confirm(
                `Apakah Anda yakin ingin menghapus admin "${adminUsername}"?`
            )
        ) {
            $.ajax({
                url: `${BASE_URL}/hapus_admin/${adminId}`,
                type: "DELETE",
                success: function (response) {
                    showFeedback(
                        response.message || "Admin berhasil dihapus.",
                        "success"
                    );
                    if (adminTable) adminTable.ajax.reload(null, false);
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
        const adminId = $("#admin_id").val();
        const url = adminId
            ? `${BASE_URL}/update_admin/${adminId}`
            : `${BASE_URL}/simpan_admin`;
        const method = "POST";

        const formData = new FormData(adminForm[0]);
        if (adminId) {
            formData.append("_method", "PUT");
            if (formData.get("password") === "") {
                formData.delete("password");
                formData.delete("password_confirmation");
            }
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
                if (adminModal) adminModal.hide();
                if (adminTable) adminTable.ajax.reload(null, false);
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
                    .text(adminId ? "Simpan Perubahan" : "Tambah");
            },
        });
    });

    if (adminModalElement) {
        adminModalElement.addEventListener("hidden.bs.modal", function (event) {
            clearForm();
        });
    }

    function setFormState(state, id = null) {
        clearForm();
        if (state === "add") {
            modalTitle.text("Tambah Admin Baru");
            $("#form_method").val("POST");
            $("#admin_id").val("");
            passwordInput
                .attr("placeholder", "Masukkan password (min. 8 karakter)")
                .prop("required", true);
            passwordConfirmationInput.prop("required", true);
            btnSave.text("Tambah");
        } else if (state === "edit") {
            modalTitle.text("Edit Admin");
            $("#form_method").val("PUT");
            $("#admin_id").val(id);
            passwordInput
                .attr("placeholder", "Kosongkan jika tidak ingin ubah password")
                .prop("required", false);
            passwordConfirmationInput.prop("required", false);
            btnSave.text("Simpan Perubahan");
        }
    }

    function clearForm() {
        adminForm[0].reset();
        $("#admin_id").val("");
        $("#form_method").val("");
        passwordInput.attr(
            "placeholder",
            "Masukkan password (min. 8 karakter)"
        );
        clearErrors();
    }

    function clearErrors() {
        adminForm.find(".is-invalid").removeClass("is-invalid");
        adminForm.find(".invalid-feedback").text("");
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
            }

            const targetErrorDivId =
                field === "password" && errors["password_confirmation"]
                    ? "password_confirmation-error"
                    : `${field}-error`;
            const targetErrorDiv = $(`#${targetErrorDivId}`);

            if (targetErrorDiv.length) {
                if (field === "password" && errors["password_confirmation"]) {
                    const confirmInput = $("#password_confirmation");
                    if (confirmInput.length)
                        confirmInput.addClass("is-invalid");
                    targetErrorDiv.text(errors["password_confirmation"][0]);
                    if (
                        errors["password"] &&
                        errorDiv.length &&
                        field === "password"
                    ) {
                        errorDiv.text(errors["password"][0]);
                    }
                } else if (field !== "password_confirmation") {
                    targetErrorDiv.text(errors[field][0]);
                }
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
            const fieldToFocus = errors["password_confirmation"]
                ? "password_confirmation"
                : firstErrorField;
            const errorInput = $(`#${fieldToFocus}`);
            if (errorInput.length && errorInput.is(":visible")) {
                errorInput.focus();
            } else if ($(`#${firstErrorField}`).length) {
                $(`#${firstErrorField}`).focus();
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
