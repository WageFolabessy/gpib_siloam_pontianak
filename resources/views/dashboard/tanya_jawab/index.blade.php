@extends('dashboard.components.main')

@section('title')
    - Template Tanya Jawab
@endsection

@section('content')
    @include('dashboard.tanya_jawab.modal-tambah')

    @include('dashboard.tanya_jawab.detail-tanya-jawab')

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Template Tanya Jawab</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="templateTanyaJawabTable" width="100%" cellspacing="0">
                    <caption>Daftar Template Tanya Jawab</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col">Pertanyaan</th>
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
    <script src="{{ asset('assets/dashboard/js/dashboard-tanya_jawab.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#btn-add-tanya-jawab-header').on('click', function() {
                $('#btn-add-tanya-jawab').click();
            });
        });
    </script>
@endsection
