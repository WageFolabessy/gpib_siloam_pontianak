@extends('components.main')

@section('title')
    - Jadwal Ibadah
@endsection

@section('style')
    <style>
        .speakable {
            cursor: pointer;
        }

        .card-header.bg-chocolate {
            background-color: #8B4513;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4 display-6 speakable text-brown">Jadwal Ibadah Rutin</h2>

        <div class="card mb-4 speakable" id="jadwal-minggu-card">
            <div class="card-header bg-chocolate text-white">
                <h5 class="card-title text-center mb-0">Jadwal Ibadah Minggu</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center table-striped mb-0">
                        <caption class="visually-hidden">Jadwal Ibadah Minggu</caption>
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $ibadahMingguExists = false; @endphp
                            @forelse ($jadwalIbadah->where('kategori', 'Ibadah Minggu') as $jadwal)
                                @php $ibadahMingguExists = true; @endphp
                                <tr>
                                    <td>{{ $jadwal->keterangan }}</td>
                                    <td>{{ $jadwal->jam }} WIB</td>
                                </tr>
                            @empty
                            @endforelse
                            @if (!$ibadahMingguExists && $jadwalIbadah->where('kategori', 'Ibadah Minggu')->isEmpty())
                                <tr>
                                    <td colspan="2" class="text-muted">Tidak ada jadwal Ibadah Minggu untuk ditampilkan.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4 speakable" id="jadwal-pelkat-card">
            <div class="card-header bg-chocolate text-white">
                <h5 class="card-title text-center mb-0">Jadwal Ibadah Pelkat</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center table-striped mb-0">
                        <caption class="visually-hidden">Jadwal Ibadah Pelkat</caption>
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Hari</th>
                                <th scope="col">Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $ibadahPelkatExists = false; @endphp
                            @forelse ($jadwalIbadah->where('kategori', 'Ibadah Pelkat') as $jadwal)
                                @php $ibadahPelkatExists = true; @endphp
                                <tr>
                                    <td>{{ $jadwal->keterangan }}</td>
                                    <td>{{ $jadwal->hari }}</td>
                                    <td>{{ $jadwal->jam }} WIB</td>
                                </tr>
                            @empty
                            @endforelse
                            @if (!$ibadahPelkatExists && $jadwalIbadah->where('kategori', 'Ibadah Pelkat')->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-muted">Tidak ada jadwal Ibadah Pelkat untuk ditampilkan.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4 mb-5">
            {{ $jadwalIbadah->links() }}
        </div>

    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/pages/js/speechsynthesis/jadwal-ibadah.js') }}"></script>
@endsection
