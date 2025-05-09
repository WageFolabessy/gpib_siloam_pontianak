@extends('components.main')

@section('title')
    - Renungan
@endsection

@section('style')
    <style>
        .renungan-card-img-top {
            height: 200px;
            object-fit: cover;
            object-position: center;
            width: 100%;
        }

        .speakable-highlight {
            background-color: #fff3cd !important;
            color: #0a3678;
            border-radius: 0.25rem;
            padding: 0.15rem 0.25rem;
            box-shadow: 0 0 5px rgba(255, 193, 7, 0.5) !important;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, color 0.3s ease;
        }

        #load-more-spinner {
            display: none;
        }

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
    <div class="speakable" id="renungan-header-section">
        <div class="container mt-5">
            <div class="p-5 mb-4 bg-light rounded-3 text-center shadow-sm">
                <h1 class="display-6 text-brown"><span class="tts-segment">Renungan</span></h1>
                <p class="lead text-brown"><span class="tts-segment">Dalam pelukan hangat renungan rohani ini. Semoga setiap
                        kata yang
                        terhampar di halaman ini menjadi sumber kekuatan dan ketenangan bagi hati dan jiwa Anda.
                        Bersama-sama, mari kita renungkan Firman-Nya dan temukan cahaya-Nya yang memandu langkah-langkah
                        kita.</span></p>
                <hr class="my-4" />
                <p><span class="tts-segment text-brown">Tuhan memberkati perjalanan rohaniah kita bersama!</span></p>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center" id="renungan-container">
            @include('pages.renungan.renungan-card', ['renungan' => $renungan])
        </div>

        <div id="load-more-container" class="text-center mt-3 mb-5 {{ !$renungan->hasMorePages() ? 'd-none' : '' }}">
            <button id="load-more" class="btn btn-outline-brown px-4 py-2"
                data-nextpage="{{ $renungan->hasMorePages() ? $renungan->nextPageUrl() : '' }}"
                data-baseurl="{{ route('renungan.loadmore') }}">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                    id="load-more-spinner"></span>
                <span id="load-more-text">Muat Lebih Banyak</span>
            </button>
        </div>
        <div id="no-more-renungan" class="text-center text-muted my-4"
            style="display: {{ !$renungan->hasMorePages() && $renungan->total() > 0 && $renungan->count() > 0 ? 'block' : 'none' }};">
            <p>Tidak ada renungan lagi.</p>
        </div>
        @if ($renungan->total() === 0)
            <div class="col-12">
                <p class="text-center text-muted mt-4">Belum ada renungan untuk ditampilkan.</p>
            </div>
        @endif
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/pages/js/renungan/renungan.js') }}"></script>
    <script src="{{ asset('assets/pages/js/speechsynthesis/renungan.js') }}"></script>
@endsection
