$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const jadwalModalElement = document.getElementById("jadwalIbadahModal");
    const jadwalModal = jadwalModalElement
        ? new bootstrap.Modal(jadwalModalElement)
        : null;
    const modalTitle = $("#jadwalIbadahModalLabel");
    const jadwalForm = $("#jadwalIbadahForm");
    const btnSave = $("#btn-save-jadwal");
    const errorAlert = $("#error-alert");

    let jadwalTable;
    if ($.fn.DataTable) {
        jadwalTable = $("#jadwalIbadahTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/dashboard/jadwal_ibadah/jadwal_ibadahTable",
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
                { data: "keterangan", name: "keterangan", width: "30%" },
                { data: "hari", name: "hari", width: "10%" },
                {
                    data: "jam",
                    name: "jam",
                    width: "10%",
                    className: "text-center",
                },
                { data: "kategori", name: "kategori", width: "15%" },
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
            order: [[5, "desc"]],
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

    $("#btn-add-jadwal").click(function () {
        setFormState("add");
        if (jadwalModal) jadwalModal.show();
    });
    $("#btn-add-jadwal-header").click(function () {
        $("#btn-add-jadwal").click();
    });

    $("#jadwalIbadahTable").on("click", ".tombol-edit-jadwal", function () {
        const jadwalId = $(this).data("id");
        if (!jadwalId) {
            console.error("Edit button clicked without data-id");
            return;
        }

        setFormState("edit", jadwalId);

        $.ajax({
            url: `/dashboard/jadwal_ibadah/edit_jadwal_ibadah/${jadwalId}`,
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
                    showFeedback("Data jadwal tidak ditemukan.", "error");
                    if (jadwalModal) jadwalModal.hide();
                    return;
                }
                $("#jadwal_ibadah_id").val(data.id);
                $("#keterangan").val(data.keterangan);
                $("#hari").val(data.hari);
                $("#jam").val(data.jam);
                $("#kategori").val(data.kategori);

                if (jadwalModal) jadwalModal.show();
            },
            error: function (xhr) {
                showFeedback("Gagal memuat data jadwal untuk diedit.", "error");
                console.error("Edit AJAX Error:", xhr.responseText);
            },
            complete: function () {
                btnSave
                    .prop("disabled", false)
                    .text(
                        $("#form_method").val() === "PUT"
                            ? "Simpan Perubahan"
                            : "Tambah"
                    );
            },
        });
    });

    $("#jadwalIbadahTable").on("click", ".tombol-hapus-jadwal", function () {
        const jadwalId = $(this).data("id");
        const jadwalKeterangan = $(this).data("keterangan") || "ini";
        if (!jadwalId) {
            console.error("Delete button clicked without data-id");
            return;
        }

        if (
            confirm(
                `Apakah Anda yakin ingin menghapus jadwal "${jadwalKeterangan}"?`
            )
        ) {
            $.ajax({
                url: `/dashboard/jadwal_ibadah/hapus_jadwal_ibadah/${jadwalId}`,
                type: "DELETE",
                success: function (response) {
                    showFeedback(
                        response.message || "Jadwal Ibadah berhasil dihapus.",
                        "success"
                    );
                    if (jadwalTable) jadwalTable.ajax.reload(null, false);
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
        const jadwalId = $("#jadwal_ibadah_id").val();
        const url = jadwalId
            ? `/dashboard/jadwal_ibadah/update_jadwal_ibadah/${jadwalId}`
            : "/dashboard/jadwal_ibadah/simpan_jadwal_ibadah";
        const method = "POST";

        const formData = new FormData(jadwalForm[0]);
        if (jadwalId) {
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
                if (jadwalModal) jadwalModal.hide();
                if (jadwalTable) jadwalTable.ajax.reload(null, false);
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
                    .text(jadwalId ? "Simpan Perubahan" : "Tambah");
            },
        });
    });

    if (jadwalModalElement) {
        jadwalModalElement.addEventListener(
            "hidden.bs.modal",
            function (event) {
                clearForm();
            }
        );
    }

    function setFormState(state, id = null) {
        clearForm();
        if (state === "add") {
            modalTitle.text("Tambah Jadwal Ibadah Baru");
            $("#form_method").val("POST");
            $("#jadwal_ibadah_id").val("");
            btnSave.text("Tambah");
        } else if (state === "edit") {
            modalTitle.text("Edit Jadwal Ibadah");
            $("#form_method").val("PUT");
            $("#jadwal_ibadah_id").val(id);
            btnSave.text("Simpan Perubahan");
        }
    }

    function clearForm() {
        jadwalForm[0].reset();
        $("#jadwal_ibadah_id").val("");
        $("#form_method").val("");
        clearErrors();
    }

    function clearErrors() {
        jadwalForm.find(".is-invalid").removeClass("is-invalid");
        jadwalForm.find(".invalid-feedback").text("");
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
