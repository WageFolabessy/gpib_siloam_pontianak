@extends('dashboard.components.main')
@section('title')
    - Template Tanya Jawab
@endsection
@section('content')
    <!-- Page Heading -->
    @include('dashboard.tanya_jawab.modal-tambah')
    @include('dashboard.tanya_jawab.detail-tanya-jawab')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Template Tanya Jawab</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="templateTanyaJawabTable" width="100%" cellspacing="0">
                    <caption>Daftar Template Tanya Jawab</caption>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Pertanyaan</th>
                            <th scope="col">Jawaban</th>
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
    <script src="{{ asset('assets/dashboard/js/dashboard-tanya_jawab.js') }}"></script>
@endsection
