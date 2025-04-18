@forelse ($renungan as $data)
    <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch speakable renungan-card-item">
        <div class="card shadow-sm h-100">
            @if ($data->thumbnail)
                <img src="{{ asset('/storage/' . $data->thumbnail) }}" class="card-img-top renungan-card-img-top"
                    alt="{{ $data->judul }}">
            @endif
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><span class="tts-segment">{{ $data->judul }}</span></h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    <i class="fas fa-book-bible me-1"></i>
                    <span class="tts-segment">{{ $data->alkitab ?? 'N/A' }}</span>
                </h6>
                <a href="{{ route('detail-renungan', $data->slug) }}" class="btn btn-primary mt-auto w-100">
                    Baca Selengkapnya
                </a>
            </div>
        </div>
    </div>
@empty
@endforelse
