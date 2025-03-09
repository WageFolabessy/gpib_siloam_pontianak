<footer class="navbar navbar-expand-lg navbar-light bg-light border-top1 mt-4">
    <div class="container">
        <p class="navbar-text col-md-4 mb-0 footer-text">
            &copy; GPIB SILOAM PONTIANAK
        </p>

        <a href="/"
            class="navbar-brand col-md-4 d-flex align-items-center justify-content-center
                    mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <img src="{{ asset('assets/pages/img/logo-80.png') }}" alt="Logo" width="80" height="80"
                class="d-inline-block align-text-center" />
        </a>

        <ul class="navbar-nav col-md-4 justify-content-end">
            <li class="nav-item">
                <a href="{{ route('beranda') }}" class="nav-link px-2 footer-text">Beranda</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('jadwal-ibadah') }}" class="nav-link px-2 footer-text">Jadwal Ibadah</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('renungan') }}" class="nav-link px-2 footer-text">Renungan</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('info') }}" class="nav-link px-2 footer-text">Sejarah</a>
            </li>
        </ul>
    </div>
</footer>
