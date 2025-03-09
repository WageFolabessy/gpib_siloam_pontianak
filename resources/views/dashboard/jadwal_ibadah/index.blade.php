@extends('dashboard.components.main')
@section('title')
    - Jadwal Ibadah
@endsection
@section('content')
    <!-- Page Heading -->
    @include('dashboard.jadwal_ibadah.modal-tambah')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Ibadah</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="jadwalIbadahTable" width="100%" cellspacing="0">
                    <caption>Daftar Jadwal Ibadah</caption>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Hari</th>
                            <th scope="col">Jam</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Tanggal Dibuat</th>
                            <th scope="col">Tanggal Diperbaharui</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/dashboard/js/dashboard-jadwal_ibadah.js') }}"></script>
@endsection
