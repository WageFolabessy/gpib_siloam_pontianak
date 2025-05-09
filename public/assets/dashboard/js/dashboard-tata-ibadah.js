$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const tataIbadahModalElement = document.getElementById("tataIbadahModal");
    const tataIbadahModal = tataIbadahModalElement
        ? new bootstrap.Modal(tataIbadahModalElement)
        : null;
    const modalTitle = $("#tataIbadahModalLabel");
    const tataIbadahForm = $("#tataIbadahForm");
    const btnSave = $("#btn-save-ti");
    const errorAlert = $("#error-alert-ti");

    const filePdfInput = $("#file_pdf_ti");
    const currentFileInfo = $("#current-file-info-ti");
    const currentFileLink = $("#current-file-link-ti");
    const currentFileName = $("#current-file-name-ti");
    const filePdfRequiredIndicator = $("#file_pdf_ti_required_indicator");

    const BASE_URL = "/dashboard/tata_ibadah";

    let tataIbadahTable;
    if ($.fn.DataTable) {
        tataIbadahTable = $("#tataIbadahTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${BASE_URL}/tataIbadahTable`,
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

    $("#btn-add-tata-ibadah").click(function () {
        setFormState("add");
        if (tataIbadahModal) tataIbadahModal.show();
    });

    $("body").on("click", ".btn-edit-tata-ibadah", function () {
        const tataIbadahId = $(this).data("id");
        if (!tataIbadahId) return;

        setFormState("edit", tataIbadahId);

        $.ajax({
            url: `${BASE_URL}/edit_tata_ibadah/${tataIbadahId}`,
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
                    showFeedback("Data Tata Ibadah tidak ditemukan.", "error");
                    return;
                }
                $("#tata_ibadah_id").val(data.id);
                $("#judul_ti").val(data.judul);
                $("#tanggal_terbit_ti").val(
                    data.tanggal_terbit &&
                        typeof data.tanggal_terbit === "string" &&
                        data.tanggal_terbit.trim() !== ""
                        ? (function (dateString) {
                              try {
                                  const dateObj = new Date(dateString);
                                  if (isNaN(dateObj.getTime())) {
                                      if (
                                          dateString.match(/^\d{4}-\d{2}-\d{2}/)
                                      ) {
                                          return dateString.substring(0, 10);
                                      }
                                      return "";
                                  }
                                  const year = dateObj.getFullYear();
                                  const month = String(
                                      dateObj.getMonth() + 1
                                  ).padStart(2, "0");
                                  const day = String(
                                      dateObj.getDate()
                                  ).padStart(2, "0");
                                  return `${year}-${month}-${day}`;
                              } catch (e) {
                                  return "";
                              }
                          })(data.tanggal_terbit)
                        : ""
                );
                $("#is_published_ti").prop("checked", data.is_published);

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
                if (tataIbadahModal) tataIbadahModal.show();
            },
            error: function (xhr) {
                showFeedback(
                    "Gagal memuat data Tata Ibadah untuk diedit. " +
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

    $("body").on("click", ".btn-delete-tata-ibadah", function () {
        const tataIbadahId = $(this).data("id");
        const tataIbadahJudul = $(this).data("judul");
        if (!tataIbadahId) return;

        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Apakah Anda yakin?",
                html: `Anda akan menghapus Tata Ibadah: <br><strong>"${tataIbadahJudul}"</strong>`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete(tataIbadahId);
                }
            });
        } else {
            if (
                confirm(
                    `Apakah Anda yakin ingin menghapus Tata Ibadah "${tataIbadahJudul}"?`
                )
            ) {
                performDelete(tataIbadahId);
            }
        }
    });

    function performDelete(id) {
        $.ajax({
            url: `${BASE_URL}/hapus_tata_ibadah/${id}`,
            type: "DELETE",
            success: function (response) {
                showFeedback(
                    response.message || "Tata Ibadah berhasil dihapus.",
                    "success",
                    true
                );
                if (tataIbadahTable) tataIbadahTable.ajax.reload(null, false);
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
        const tataIbadahId = $("#tata_ibadah_id").val();
        const url = tataIbadahId
            ? `${BASE_URL}/update_tata_ibadah/${tataIbadahId}`
            : `${BASE_URL}/simpan_tata_ibadah`;
        const method = "POST";

        const formData = new FormData(tataIbadahForm[0]);

        if (tataIbadahId) {
            formData.append("_method", "PUT");
        }
        if (!$("#is_published_ti").is(":checked")) {
            formData.set("is_published", "0");
        } else {
            formData.set("is_published", "1");
        }

        clearAllErrors();
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
                if (tataIbadahModal) tataIbadahModal.hide();
                if (tataIbadahTable) tataIbadahTable.ajax.reload(null, false);
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
                    .text(tataIbadahId ? "Simpan Perubahan" : "Tambah");
            },
        });
    });

    if (tataIbadahModalElement) {
        tataIbadahModalElement.addEventListener(
            "hidden.bs.modal",
            function (event) {
                clearFormAndErrors();
            }
        );
    }

    filePdfInput.change(function () {
        const file = this.files[0];
        $(this).removeClass("is-invalid");
        $("#file_pdf_ti-error").text("");
        currentFileInfo.addClass("d-none");

        if (file) {
            if (file.type === "application/pdf") {
                const maxSize = 20 * 1024 * 1024; // 20MB
                if (file.size > maxSize) {
                    $(this).addClass("is-invalid").val("");
                    $("#file_pdf_ti-error").text(
                        `Ukuran file maks ${maxSize / 1024 / 1024} MB.`
                    );
                    return;
                }
                currentFileName.text(file.name);
                currentFileLink.removeAttr("href").addClass("d-none");
                currentFileInfo.removeClass("d-none");
            } else {
                $(this).addClass("is-invalid").val("");
                $("#file_pdf_ti-error").text("File harus berformat PDF.");
            }
        }
    });

    function setFormState(state, id = null) {
        clearFormAndErrors();
        if (state === "add") {
            modalTitle.text("Tambah Tata Ibadah Baru");
            btnSave.text("Tambah");
            filePdfInput.attr("required", true);
            filePdfRequiredIndicator.removeClass("d-none");
        } else if (state === "edit") {
            modalTitle.text("Edit Tata Ibadah");
            $("#tata_ibadah_id").val(id);
            btnSave.text("Simpan Perubahan");
            filePdfInput.removeAttr("required");
            filePdfRequiredIndicator.addClass("d-none");
        }
    }

    function clearFormAndErrors() {
        tataIbadahForm[0].reset();
        $("#tata_ibadah_id").val("");
        currentFileInfo.addClass("d-none");
        currentFileLink.removeAttr("href").addClass("d-none");
        $("#is_published_ti").prop("checked", true);
        clearAllErrors();
        filePdfInput.attr("required", true);
        filePdfRequiredIndicator.removeClass("d-none");
    }

    function clearAllErrors() {
        tataIbadahForm.find(".is-invalid").removeClass("is-invalid");
        tataIbadahForm.find(".invalid-feedback").text("");
        errorAlert.addClass("d-none").html("");
    }

    function displayValidationErrors(errors) {
        clearAllErrors();
        let firstErrorField = null;
        let errorSummaryHtml = "<ul>";

        for (const field in errors) {
            if (!firstErrorField) firstErrorField = field;

            let targetInput;
            let targetErrorDiv;

            if (field === "file_pdf") {
                targetInput = filePdfInput;
                targetErrorDiv = $("#file_pdf_ti-error");
            } else if (field === "judul") {
                targetInput = $("#judul_ti");
                targetErrorDiv = $("#judul_ti-error");
            } else if (field === "tanggal_terbit") {
                targetInput = $("#tanggal_terbit_ti");
                targetErrorDiv = $("#tanggal_terbit_ti-error");
            } else if (field === "is_published") {
                targetInput = $("#is_published_ti");
                targetErrorDiv = $("#is_published_ti-error");
            } else {
                targetInput = $(`#${field}_ti`);
                targetErrorDiv = $(`#${field}_ti-error`);
            }

            if (targetInput && targetInput.length) {
                targetInput.addClass("is-invalid");
            }
            if (targetErrorDiv && targetErrorDiv.length) {
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
            const errorInputToFocus = $(`#${firstErrorField}_ti`);
            if (firstErrorField === "file_pdf") filePdfInput.focus();
            else if (firstErrorField === "judul") $("#judul_ti").focus();
            else if (firstErrorField === "tanggal_terbit")
                $("#tanggal_terbit_ti").focus();
            else if (
                errorInputToFocus.length &&
                errorInputToFocus.is(":visible")
            )
                errorInputToFocus.focus();
        }
    }

    function showFeedback(message, type = "info", useSwal = false) {
        console.log(`Feedback (${type}): ${message}`);
        if (useSwal && typeof Swal !== "undefined") {
            Swal.fire({
                icon: type,
                title: type.charAt(0).toUpperCase() + type.slice(1) + "!",
                text: message,
                timer: type === "success" ? 2000 : 3500,
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
