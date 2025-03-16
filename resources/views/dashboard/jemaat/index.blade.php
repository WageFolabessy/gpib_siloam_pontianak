@extends('dashboard.components.main')
@section('title')
    - Jemaat
@endsection
@section('content')
    <!-- Page Heading -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jemaat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="jemaatTable" width="100%" cellspacing="0">
                    <caption>Daftar Jemaat</caption>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
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
    <script src="{{ asset('assets/dashboard/js/dashboard-jemaat.js') }}"></script>

@endsection
