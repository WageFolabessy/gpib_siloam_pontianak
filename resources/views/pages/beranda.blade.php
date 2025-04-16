@extends('components.main')

@section('css')
    <style>
        .renungan-card-img-top {
            height: 200px;
            object-fit: cover;
            object-position: center;
            width: 100%;
        }

        .speakable {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    {{-- COVER --}}
    <header id="autoSpeak" class="text-center p-5 mb-4 bg-white shadow-sm speakable">
        <h1 class="display-5 fw-bold">- GPIB SILOAM PONTIANAK -</h1>
        <figure class="mt-4">
            <blockquote class="blockquote fs-5">
                <p>
                    “Dan orang akan datang dari Timur dan Barat dan dari Utara dan Selatan
                    dan mereka duduk makan di dalam Kerajaan Allah”
                </p>
            </blockquote>
            <figcaption class="blockquote-footer mt-2 speakable">
                Lukas 13:29
            </figcaption>
        </figure>
        <a href="{{ route('info') }}" class="btn btn-outline-primary btn-lg px-4 mt-4">Sejarah Gereja</a>
    </header>
    {{-- Akhir COVER --}}

    {{-- RENUNGAN --}}
    @if ($renungan->isNotEmpty())
        <div class="container mt-5">
            <h2 class="text-center mb-4 display-6 speakable">Renungan Terbaru</h2>
            <div class="row justify-content-center">
                @foreach ($renungan as $data)
                    <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch speakable">
                        <div class="card shadow-sm h-100">
                            @if ($data->thumbnail)
                                <img src="{{ asset('/storage/' . $data->thumbnail) }}"
                                    class="card-img-top renungan-card-img-top" alt="{{ $data->judul }}">
                            @else
                                <img src="{{ asset('assets/pages/img/placeholder-gereja.png') }}"
                                    class="card-img-top renungan-card-img-top" alt="Placeholder Renungan">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $data->judul }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-book-bible me-1"></i>
                                    {{ $data->alkitab ?? 'N/A' }}
                                </h6>
                                <a href="{{ route('detail-renungan', $data->slug) }}" class="btn btn-primary mt-auto w-100">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4 mb-5">
                <a href="{{ route('renungan') }}" class="btn btn-outline-secondary">Lihat Semua Renungan</a>
            </div>
        </div>
    @endif
    {{-- AKHIR RENUNGAN --}}
@endsection

@section('script')
    <script src="{{ asset('assets/pages/js/speechsynthesis/beranda.js') }}"></script>
@endsection
