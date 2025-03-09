@extends('dashboard.components.main')
@section('title')
    - Pendeta
@endsection
@section('content')
    <!-- Page Heading -->
    @include('dashboard.pendeta.modal-tambah')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengurus</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pendetaTable" width="100%" cellspacing="0">
                    <caption>Daftar Pengurus</caption>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama</th>
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
    <script src="{{ asset('assets/dashboard/js/dashboard-pendeta.js') }}"></script>

@endsection
