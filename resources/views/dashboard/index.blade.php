@extends('dashboard.components.main')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Selamat datang kembali, <strong>{{ $adminUserName ?? 'Admin' }}</strong>!
                Anda berada di halaman dashboard admin GPIB Jemaat Siloam Pontianak.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div
                    class="card-header bg-primary text-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold"><i class="fas fa-book-open me-2"></i>Renungan</h6>
                </div>
                <div class="card-body">
                    <p class="card-text small mb-3">
                        Kelola renungan harian atau mingguan untuk inspirasi jemaat.
                    </p>
                    <a href="{{ route('dashboard.renungan') }}" class="btn btn-outline-primary btn-sm w-100">Kelola
                        Renungan <i class="fas fa-arrow-circle-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div
                    class="card-header bg-success text-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i>Jadwal Ibadah</h6>
                </div>
                <div class="card-body">
                    <p class="card-text small mb-3">
                        Kelola jadwal ibadah rutin, Pelkat, dan kegiatan gerejawi lainnya.
                    </p>
                    <a href="{{ route('dashboard.jadwal_ibadah') }}"
                        class="btn btn-outline-success btn-sm w-100">Kelola Jadwal <i
                            class="fas fa-arrow-circle-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold"><i class="fas fa-users me-2"></i>Pendeta & Majelis</h6>
                </div>
                <div class="card-body">
                    <p class="card-text small mb-3">
                        Kelola data pelayan jemaat, termasuk pendeta dan majelis.
                    </p>
                    <a href="{{ route('dashboard.pendeta') }}" class="btn btn-outline-info btn-sm w-100">Kelola
                        Pelayan <i class="fas fa-arrow-circle-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .card .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .card .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .card .text-xs {
            font-size: 0.7rem;
        }

        .no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .no-gutters>.col,
        .no-gutters>[class*="col-"] {
            padding-right: 0;
            padding-left: 0;
        }

        .card-header .badge {
            text-decoration: none;
        }

        .card-header .badge:hover {
            opacity: 0.8;
        }
    </style>
@endpush

@push('scripts')
    {{-- Jika Anda membutuhkan JS khusus untuk dashboard --}}
@endpush
