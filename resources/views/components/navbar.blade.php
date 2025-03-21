<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top border-bottom1">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('assets/pages/img/logo-80.png') }}" alt="Logo" width="80" height="80"
                class="d-inline-block align-text-center">
            GPIB SILOAM PONTIANAK
        </a>
        <button class="navbar-toggler w-100" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('jadwal-ibadah') ? 'active' : '' }}"
                        href="{{ url('jadwal-ibadah') }}">Jadwal Ibadah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('renungan') ? 'active' : '' }}"
                        href="{{ url('renungan') }}">Renungan</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link {{ request()->is('info') ? 'active' : '' }} dropdown-toggle" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Tentang Gereja
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ url('info') }}">Info</a></li>
                    </ul>
                </li>
                @if (Auth::user())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('profil') }}">Profil Saya</a></li>
                            <li>
                                <form method="POST" action="{{ route('jemaat_logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"
                                        style="border: none; background: none; cursor: pointer;">
                                        Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-primary" href="{{ route('pages.login') }}">Masuk</a>
                    </li>
                @endif
                <!-- Tombol toggle untuk speech synthesis -->
                <li class="nav-item ms-2">
                    <a id="btnToggleSpeech" class="btn btn-secondary" href="#" type="button"></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
