@extends('components.main')

@section('title')
    - {{ $renungan->judul }}
@endsection

@section('style')
    <style>
        #speechPlayer {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            border-bottom: 1px solid #eaeaea;
            border-radius: 0.3rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            transition: top 0.3s ease-in-out;
        }

        #speechPlayer button {
            background: none;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            color: #6c757d;
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
            margin: 0 5px;
        }

        #speechPlayer button:not(:disabled):hover {
            background-color: #e9ecef;
            color: #343a40;
            transform: scale(1.05);
        }

        #speechPlayer button:not(:disabled):active {
            transform: scale(0.98);
        }

        #speechPlayer button#playPauseBtn.playing {
            color: #8B4513;
            background-color: #fdf8f5;
        }

        #speechPlayer button#playPauseBtn.playing:hover {
            color: #7a3c10;
            background-color: #f8f0eb;
        }

        #speechPlayer button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background-color: transparent !important;
            transform: none !important;
        }

        .renungan-content img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
            border-radius: 0.25rem;
        }

        .renungan-content p,
        #renungan-meta h1,
        #renungan-meta h6 {
            line-height: 1.7;
            margin-bottom: 1rem;
            text-align: justify;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #renungan-meta h1,
        #renungan-meta h6 {
            text-align: center;
        }

        .navigation-buttons a.btn {
            background-color: #8B4513;
            /* Warna tema Anda */
            color: white;
            min-width: 40px;
        }

        .navigation-buttons a.btn:hover {
            background-color: #7a3c10;
            /* Warna tema hover */
            color: white;
        }

        .navigation-buttons a.btn.disabled {
            background-color: #c8a98f;
            border-color: #c8a98f;
            pointer-events: none;
        }

        .navigation-buttons a.btn-outline-secondary {
            background-color: transparent;
            color: #6c757d;
            border-color: #6c757d;
        }

        .navigation-buttons a.btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        .speaking-highlight {
            background-color: #fff3cd;
            border-radius: 0.25rem;
            box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">

                <div id="speechPlayer">
                    <button id="playPauseBtn" title="Play/Pause Seluruh Renungan"
                        aria-label="Play/Pause Pembaca Teks Seluruh Renungan">
                        <i class="fas fa-play"></i>
                    </button>
                    <button id="stopBtn" title="Stop" aria-label="Hentikan Pembaca Teks">
                        <i class="fas fa-stop"></i>
                    </button>
                </div>

                <article class="bg-white p-4 p-md-5 rounded shadow-sm">
                    <div id="renungan-meta" class="text-center border-bottom pb-3 mb-4">
                        <h1>{{ $renungan->judul }}</h1>
                        @if ($renungan->alkitab)
                            <h6 class="text-muted mt-2 mb-1"><i class="fas fa-book-bible me-1"></i> {{ $renungan->alkitab }}
                            </h6>
                        @endif
                        @if ($renungan->bacaan_alkitab)
                            <h6 class="text-muted mb-3 fst-italic">{{ $renungan->bacaan_alkitab }}</h6>
                        @endif
                    </div>

                    @if ($renungan->thumbnail)
                        <div class="text-center mb-4">
                            <img src="{{ asset('/storage/' . $renungan->thumbnail) }}" class="img-fluid rounded"
                                style="max-height: 400px;" alt="{{ $renungan->judul }}">
                        </div>
                    @endif

                    <div id="renungan-body-content" class="renungan-content">
                      <p>{!! html_entity_decode($renungan->isi_bacaan) !!}</p>
                        
                    </div>

                    <hr class="my-4">
                    <p class="text-muted small text-end mb-4">Diupload: <span
                            class="fw-bold">{{ $diupload ?? $renungan->updated_at?->locale('id')->isoFormat('D MMMM<y_bin_46>INFO HH:mm') }}</span>
                    </p>

                    @php
                        $prevUrl = $prevRenungan ? route('detail-renungan', ['renungan' => $prevRenungan->slug]) : '#';
                        $prevTitle = $prevRenungan
                            ? 'Sebelumnya: ' . e($prevRenungan->judul)
                            : 'Tidak ada renungan sebelumnya';
                        $prevDisabledClass = !$prevRenungan ? 'disabled' : '';
                        $nextUrl = $nextRenungan ? route('detail-renungan', ['renungan' => $nextRenungan->slug]) : '#';
                        $nextTitle = $nextRenungan
                            ? 'Berikutnya: ' . e($nextRenungan->judul)
                            : 'Tidak ada renungan berikutnya';
                        $nextDisabledClass = !$nextRenungan ? 'disabled' : '';
                    @endphp
                    <nav class="d-flex justify-content-between mt-4 navigation-buttons" aria-label="Navigasi Renungan">
                        <a href="{{ $prevUrl }}" class="btn {{ $prevDisabledClass }}" title="{{ $prevTitle }}"><i
                                class="fas fa-chevron-left"></i></a>
                        <a href="{{ route('renungan') }}" class="btn btn-outline-secondary"><i class="fas fa-list-ul"></i>
                            Daftar Renungan</a>
                        <a href="{{ $nextUrl }}" class="btn {{ $nextDisabledClass }}" title="{{ $nextTitle }}"><i
                                class="fas fa-chevron-right"></i></a>
                    </nav>
                </article>

            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Pastikan path JS ini benar --}}
    <script src="{{ asset('assets/pages/js/speechsynthesis/detail-renungan.js') }}"></script>
@endsection
