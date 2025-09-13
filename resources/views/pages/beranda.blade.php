@extends('components.main')

@section('style')
    <style>
        .renungan-card-img-top {
            display: block;
            width: 100%;
            height: 200px;
            object-fit: cover;
            object-position: center;
            background-color: #f0f0f0;
            border-top-left-radius: var(--bs-card-inner-border-radius, 0.375rem);
            border-top-right-radius: var(--bs-card-inner-border-radius, 0.375rem);
        }

        .speakable {
            cursor: pointer;
        }

        .renungan-card-item .card {
            width: 100%;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: 1px solid var(--bs-border-color-translucent, rgba(0, 0, 0, 0.1));
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .renungan-card-item .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }

        .renungan-card-item .card-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .renungan-card-item .card-title {
            font-weight: 600;
            margin-bottom: 0.5rem;

        }

        .renungan-card-item .card-subtitle {
            font-size: 0.9em;
            margin-bottom: 1rem;
        }

        .renungan-card-item .btn-brown {
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
        }

        .renungan-card-item .card-body .mt-auto {
            margin-top: auto;
        }
    </style>
@endsection

@section('content')
    {{-- COVER --}}
    <header id="autoSpeak" class="text-center p-5 mb-4 bg-white shadow-sm speakable">
        <h1 class="display-5 fw-bold">- SISTEM INFORMASI GEREJA -</h1>
        <figure class="mt-4">
            <blockquote class="blockquote fs-5">
                <p>
                    “Dan orang akan datang dari Timur dan Barat dan dari Utara dan Selatan
                    dan mereka duduk makan di dalam Kerajaan Allah”
                </p>
            </blockquote>
            <figcaption class="blockquote-footer mt-2 speakable text-white">
                Lukas 13:29
            </figcaption>
        </figure>
        <a href="{{ route('info') }}" class="btn btn-brown btn-lg px-4 mt-4">Sejarah Gereja</a>
    </header>
    {{-- Akhir COVER --}}

    {{-- RENUNGAN --}}
    @if (isset($renungan) && $renungan->isNotEmpty())
        <div class="container mt-5">
            <h2 class="text-center mb-4 display-6 speakable text-brown">Renungan Terbaru</h2>
            <div class="row justify-content-center g-4">
                @foreach ($renungan as $data)
                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch speakable renungan-card-item">
                        <div class="card shadow-sm">
                            @if ($data->thumbnail)
                                <img src="{{ asset('/storage/' . $data->thumbnail) }}"
                                    class="card-img-top renungan-card-img-top" alt="{{ $data->judul }}">
                            @else
                                <img src="{{ asset('assets/pages/img/placeholder-gereja.png') }}"
                                    class="card-img-top renungan-card-img-top" alt="Placeholder Renungan">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $data->judul }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-book-bible me-1"></i>
                                    {{ $data->alkitab ?? 'N/A' }}
                                </h6>
                                <a href="{{ route('detail-renungan', $data->slug) }}" class="btn btn-brown mt-auto w-100">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4 mb-5">
                <a href="{{ route('renungan') }}" class="btn btn-outline-brown">Lihat Semua Renungan</a>
            </div>
        </div>
    @else
        <div class="container mt-5 text-center">
            <p class="text-muted">Belum ada renungan terbaru untuk ditampilkan.</p>
        </div>
    @endif
@endsection

@section('script')
    <script src="{{ asset('assets/pages/js/speechsynthesis/beranda.js') }}"></script>
@endsection
