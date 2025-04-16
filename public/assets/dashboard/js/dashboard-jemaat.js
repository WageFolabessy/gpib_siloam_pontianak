$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const BASE_URL = "/dashboard/jemaat";

    let jemaatTable;
    if ($.fn.DataTable) {
        jemaatTable = $("#jemaatTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${BASE_URL}/jemaatTable`,
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
                { data: "name", name: "name", width: "30%" },
                { data: "email", name: "email", width: "25%" },
                { data: "created_at", name: "created_at", width: "15%" },
                { data: "updated_at", name: "updated_at", width: "15%" },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    width: "10%",
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
            },
        });
    } else {
        console.error("DataTables library is not loaded.");
    }

    $("#jemaatTable").on("click", ".tombol-hapus-jemaat", function () {
        const userId = $(this).data("id");
        const userName = $(this).data("name") || "Jemaat ini";
        if (!userId) {
            console.error("Tombol hapus diklik tanpa data-id.");
            return;
        }

        if (
            confirm(`Apakah Anda yakin ingin menghapus jemaat "${userName}"?`)
        ) {
            $.ajax({
                url: `${BASE_URL}/hapus_jemaat/${userId}`,
                type: "DELETE",
                success: function (response) {
                    showFeedback(
                        response.message || "Jemaat berhasil dihapus.",
                        "success"
                    );
                    if (jemaatTable) jemaatTable.ajax.reload(null, false);
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
