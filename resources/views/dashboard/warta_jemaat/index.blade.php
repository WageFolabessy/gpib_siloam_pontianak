@extends('dashboard.components.main')

@section('title')
    - Warta Jemaat
@endsection

@section('content')
    @include('dashboard.warta_jemaat.modal-tambah')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Warta Jemaat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="wartaJemaatTable" width="100%" cellspacing="0">
                    <caption>Daftar Warta Jemaat</caption>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Tgl Terbit</th>
                            <th scope="col">File PDF</th>
                            <th scope="col">Status</th>
                            <th scope="col">Diperbarui</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="{{ asset('assets/dashboard/js/dashboard-warta.js') }}"></script>
@endsection
