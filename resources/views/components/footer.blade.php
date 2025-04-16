<footer class="bg-light border-top mt-auto py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <small class="text-muted">&copy; {{ date('Y') }} GPIB SILOAM PONTIANAK</small>
            </div>

            <div class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0">
                <a href="{{ route('beranda') }}" class="link-body-emphasis text-decoration-none">
                    <img src="{{ asset('assets/pages/img/logo-80.png') }}" alt="Logo" width="60" height="60">
                </a>
            </div>

            <div class="col-md-4">
                <ul class="nav justify-content-center justify-content-md-end">
                    <li class="nav-item"><a href="{{ route('beranda') }}" class="nav-link px-2 text-muted">Beranda</a>
                    </li>
                    <li class="nav-item"><a href="{{ route('jadwal-ibadah') }}"
                            class="nav-link px-2 text-muted">Jadwal</a></li>
                    <li class="nav-item"><a href="{{ route('renungan') }}" class="nav-link px-2 text-muted">Renungan</a>
                    </li>
                    <li class="nav-item"><a href="{{ route('info') }}" class="nav-link px-2 text-muted">Info</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
