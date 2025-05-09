@extends('components.main')

@section('title')
    - Arsip Warta Jemaat
@endsection

@section('style')
    <style>
        .speakable-highlight {
            background-color: #fff3cd !important;
            color: #0a3678;
            border-radius: 0.25rem;
            padding: 0.15rem 0.25rem;
            box-shadow: 0 0 5px rgba(255, 193, 7, 0.5) !important;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, color 0.3s ease;
        }

        .card-header.bg-warta {
            background-color: #5D4037;
        }

        .warta-item .card-title {
            font-size: 1.25rem;
            font-weight: 500;
        }

        .warta-item .card-subtitle {
            font-size: 0.9rem;
        }

        .btn-warta-action {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .pagination {
            justify-content: center;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4 mb-5">
        <h2 class="text-center mb-5 display-6 speakable text-brown">Arsip Warta Jemaat</h2>

        @if ($wartas->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                Belum ada Warta Jemaat yang dapat ditampilkan saat ini.
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-1 g-4">
                @foreach ($wartas as $warta)
                    <div class="col">
                        <div class="card h-100 shadow-sm warta-item speakable">
                            <div class="card-header bg-warta text-white">
                                <h5 class="card-title mb-0">{{ $warta->judul }}</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Terbit: {{ $warta->tanggal_terbit->isoFormat('dddd, D MMMM YYYY') }}
                                </h6>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 text-center pb-3">
                                @if ($warta->file_pdf_path && Illuminate\Support\Facades\Storage::disk('public')->exists($warta->file_pdf_path))
                                    <a href="{{ Illuminate\Support\Facades\Storage::url($warta->file_pdf_path) }}"
                                        class="btn btn-primary btn-sm btn-warta-action" target="_blank"
                                        rel="noopener noreferrer">
                                        <i class="fas fa-eye me-1"></i> Lihat PDF
                                    </a>
                                    <a href="{{ Illuminate\Support\Facades\Storage::url($warta->file_pdf_path) }}"
                                        class="btn btn-success btn-sm btn-warta-action" download="{{ $warta->slug }}.pdf">
                                        <i class="fas fa-download me-1"></i> Unduh PDF
                                    </a>
                                @else
                                    <span class="text-muted">File PDF tidak tersedia.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($wartas->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $wartas->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/pages/js/speechsynthesis/warta-jemaat.js') }}"></script>
@endsection
