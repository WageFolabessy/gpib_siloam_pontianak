@extends('dashboard.components.main')

@section('title')
    - Admin
@endsection

@section('content')
    @include('dashboard.admin.modal-tambah')

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Admin</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="adminTable" width="100%" cellspacing="0">
                    <caption>Daftar Admin Pengguna</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col">Username</th>
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
    <script src="{{ asset('assets/dashboard/js/dashboard-admin.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#btn-add-admin-header').on('click', function() {
                $('#btn-add-admin').click();
            });
        });
    </script>
@endsection
