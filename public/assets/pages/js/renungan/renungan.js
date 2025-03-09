$(document).ready(function() {
    let offset = 0;
    let limit = 3;

    function loadRenungan() {
        $.ajax({
            url: '/get-renungan/' + offset + '/' + limit,
            method: 'GET',
            success: function(data) {
                if (data.length > 0) {
                    data.forEach(function(renungan) {
                        let bacaan = renungan.alkitab ? renungan.alkitab : '-';
                        let renunganCard = `
                            <div class="col-md-4 mb-4">
                                <div class="card" style="height: 100%">
                                    <img src="storage/thumbnails/${renungan.thumbnail}"
                                        class="card-img-top" style="height: 100%" alt="Thumbnail" />
                                    <div class="card-body">
                                        <h5 class="card-title">${renungan.judul}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            Bacaan: ${bacaan}
                                        </h6>
                                        <a href="/renungan/${renungan.slug}"
                                            class="btn btn-get-started w-100 btn-baca"
                                            data-slug="${renungan.slug}">Baca</a>

                                    </div>
                                </div>
                            </div>
                        `;
                        $('#renungan-container').append(renunganCard);
                    });
                    offset += limit;
                } else if(data.length < 0){
                    $('#load-more').hide();
                    $('#renungan-container').append(
                        '<p class="text-center">Belum ada renungan.</p>');
                }
                else {
                    $('#load-more').hide();
                    $('#renungan-container').append(
                        '<p class="text-center">Tidak ada renungan lagi.</p>');
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    // Pertama kali, muat renungan
    loadRenungan();

    // Saat tombol "Muat Lebih Banyak" diklik
    $('#load-more').click(function() {
        loadRenungan();
    });
});