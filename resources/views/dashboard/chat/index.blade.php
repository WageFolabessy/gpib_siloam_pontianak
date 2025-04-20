@extends('dashboard.components.main')

@section('title')
    - Chat
@endsection

@section('content')
    <div class="card mb-4 shadow">
        <div class="card-header py-3">
            <h6 class="font-weight-bold m-0 text-primary">Daftar Chat Pengguna</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="chatTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Pesan Terakhir</th>
                            <th>Waktu</th>
                            <th>Belum dibaca</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Include modal detail chat --}}
    @include('dashboard.chat.modal-detail-chat')
@endsection

@section('script')
    @vite('resources/js/app.js')
    <script src="{{ asset('assets/dashboard/js/chat-admin.js') }}" type="module" defer></script>
@endsection
