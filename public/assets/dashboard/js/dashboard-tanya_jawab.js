$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const tanyaJawabModalElement = document.getElementById("tanyaJawabModal");
    const tanyaJawabModal = tanyaJawabModalElement
        ? new bootstrap.Modal(tanyaJawabModalElement)
        : null;
    const addEditModalTitle = $("#tanyaJawabModalLabel");
    const tanyaJawabForm = $("#tanyaJawabForm");
    const btnSave = $("#btn-save-tanya-jawab");
    const errorAlert = $("#error-alert");

    const detailModalElement = document.getElementById("DetailTanyaJawabModal");
    const detailModal = detailModalElement
        ? new bootstrap.Modal(detailModalElement)
        : null;
    const detailPertanyaan = $("#pertanyaan_detail"); 
    const detailJawaban = $("#jawaban_detail"); //

    const BASE_URL = "/dashboard/tanya_jawab";

    let tanyaJawabTable;
    if ($.fn.DataTable) {
        tanyaJawabTable = $("#templateTanyaJawabTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${BASE_URL}/tanya_jawabTable`,
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
                { data: "pertanyaan", name: "pertanyaan", width: "40%" },
                { data: "created_at", name: "created_at", width: "15%" },
                { data: "updated_at", name: "updated_at", width: "15%" },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    width: "15%",
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
            },
        });
    } else {
        console.error("DataTables library is not loaded.");
    }


    $("#btn-add-tanya-jawab").click(function () {
        setFormState("add");
        if (tanyaJawabModal) tanyaJawabModal.show();
    });

    $("#templateTanyaJawabTable").on(
        "click",
        ".tombol-detail-tanya-jawab",
        function () {
            const templateId = $(this).data("id");
            if (!templateId) return;

            detailPertanyaan.text("Memuat...");
            detailJawaban.text("Memuat...");

            $.ajax({
                url: `${BASE_URL}/detail_tanya_jawab/${templateId}`,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    const data = response.data;
                    if (data) {
                        detailPertanyaan.text(data.pertanyaan || "-");
                        detailJawaban.text(data.jawaban || "-");
                        if (detailModal) detailModal.show();
                    } else {
                        showFeedback("Gagal memuat detail data.", "error");
                    }
                },
                error: function (xhr) {
                    showFeedback("Gagal memuat detail data.", "error");
                    console.error("Detail AJAX Error:", xhr.responseText);
                },
            });
        }
    );

    $("#templateTanyaJawabTable").on(
        "click",
        ".tombol-edit-tanya-jawab",
        function () {
            const templateId = $(this).data("id");
            if (!templateId) return;

            setFormState("edit", templateId);

            $.ajax({
                url: `${BASE_URL}/edit_tanya_jawab/${templateId}`,
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
                        showFeedback("Data Template tidak ditemukan.", "error");
                        return;
                    }
                    $("#template_tanya_jawab_id").val(data.id);
                    $("#pertanyaan").val(data.pertanyaan);
                    $("#jawaban").val(data.jawaban);

                    if (tanyaJawabModal) tanyaJawabModal.show();
                },
                error: function (xhr) {
                    showFeedback(
                        "Gagal memuat data Template untuk diedit.",
                        "error"
                    );
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
        }
    );

    $("#templateTanyaJawabTable").on(
        "click",
        ".tombol-hapus-tanya-jawab",
        function () {
            const templateId = $(this).data("id");
            const templatePertanyaan =
                $(this).data("pertanyaan") || "template ini";
            if (!templateId) return;

            if (
                confirm(
                    `Apakah Anda yakin ingin menghapus template "${templatePertanyaan}"?`
                )
            ) {
                $.ajax({
                    url: `${BASE_URL}/hapus_tanya_jawab/${templateId}`,
                    type: "DELETE",
                    success: function (response) {
                        showFeedback(
                            response.message || "Template berhasil dihapus.",
                            "success"
                        );
                        if (tanyaJawabTable)
                            tanyaJawabTable.ajax.reload(null, false);
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
        }
    );

    btnSave.click(function () {
        const templateId = $("#template_tanya_jawab_id").val();
        const url = templateId
            ? `${BASE_URL}/update_tanya_jawab/${templateId}`
            : `${BASE_URL}/simpan_tanya_jawab`;
        const method = templateId ? "POST" : "POST";

        const formData = new FormData(tanyaJawabForm[0]);
        if (templateId) {
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
                if (tanyaJawabModal) tanyaJawabModal.hide();
                if (tanyaJawabTable) tanyaJawabTable.ajax.reload(null, false);
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
                    .text(templateId ? "Simpan Perubahan" : "Tambah");
            },
        });
    });

    if (tanyaJawabModalElement) {
        tanyaJawabModalElement.addEventListener(
            "hidden.bs.modal",
            function (event) {
                clearForm();
            }
        );
    }
    if (detailModalElement) {
        detailModalElement.addEventListener(
            "hidden.bs.modal",
            function (event) {
                detailPertanyaan.text("");
                detailJawaban.text("");
            }
        );
    }

    function setFormState(state, id = null) {
        clearForm();
        if (state === "add") {
            addEditModalTitle.text("Tambah Template Tanya Jawab");
            $("#form_method").val("POST");
            $("#template_tanya_jawab_id").val("");
            btnSave.text("Tambah");
        } else if (state === "edit") {
            addEditModalTitle.text("Edit Template Tanya Jawab");
            $("#form_method").val("PUT");
            $("#template_tanya_jawab_id").val(id);
            btnSave.text("Simpan Perubahan");
        }
    }

    function clearForm() {
        tanyaJawabForm[0].reset();
        $("#template_tanya_jawab_id").val("");
        $("#form_method").val("");
        clearErrors();
    }

    function clearErrors() {
        tanyaJawabForm.find(".is-invalid").removeClass("is-invalid");
        tanyaJawabForm.find(".invalid-feedback").text("");
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
