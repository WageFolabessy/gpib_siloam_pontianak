@extends('dashboard.components.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center">Halaman Dashboard Admin</h1>
            <p class="text-center">
                Selamat datang di halaman dashboard admin gereja GPIB Jemaat Siloam Pontianak. <br>
                Di sini Anda dapat mengelola konten web gereja GPIB Jemaat Siloam Pontianak dengan mudah dan cepat.
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Renungan</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Anda dapat membuat, memperbaharui, dan menghapus renungan untuk memberi inspirasi dan motivasi
                        bagi anggota gereja GPIB Jemaat Siloam Pontianak.
                    </p>
                    <a href="{{ route('dashboard.renungan') }}" class="btn btn-primary">Kelola Renungan</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Jadwal Ibadah</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Anda dapat membuat, memperbaharui, dan menghapus jadwal ibadah untuk memberi
                        informasi dan pengumuman bagi anggota gereja GPIB Jemaat Siloam Pontianak.
                    </p>
                    <a href="{{ route('dashboard.jadwal_ibadah') }}" class="btn btn-primary">Kelola Jadwal Ibadah</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pendeta</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Anda dapat membuat, memperbaharui, dan menghapus profil pendeta, majelis
                        yang melayani di gereja GPIB Jemaat Siloam Pontianak.
                    </p>
                    <a href="{{ route('dashboard.pendeta') }}" class="btn btn-primary">Kelola Pendeta</a>
                </div>
            </div>
        </div>
    </div>
@endsection
