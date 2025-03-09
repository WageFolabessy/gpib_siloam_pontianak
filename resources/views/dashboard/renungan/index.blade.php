@extends('dashboard.components.main')
@section('title')
    - Renungan
@endsection
@section('content')
    <!-- Page Heading -->
    @include('dashboard.renungan.modal-tambah')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Renungan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="renunganTable" width="100%" cellspacing="0">
                    <caption>Daftar Renungan</caption>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Alkitab</th>
                            <th scope="col">Bacaan Alkitab</th>
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
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/n0hkd28cjwp2ck9m53jnkmilto9uz4dt6uxjv6was0o81hc2/tinymce/6/tinymce.min.js"
        referrerpolicy="origin">
    </script>
    <script src="{{ asset('assets/dashboard/js/dashboard-renungan.js') }}"></script>
@endsection
