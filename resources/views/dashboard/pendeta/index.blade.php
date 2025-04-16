@extends('dashboard.components.main')

@section('title')
    - Pendeta & Majelis
@endsection

@section('content')
    @include('dashboard.pendeta.modal-tambah')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pendeta & Majelis</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pendetaTable" width="100%" cellspacing="0">
                    <caption>Daftar Pendeta & Majelis</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Dibuat</th>
                            <th scope="col">Diupdate</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/dashboard/js/dashboard-pendeta.js') }}"></script>
@endsection