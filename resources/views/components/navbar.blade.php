<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('beranda') }}">
            <img src="{{ asset('assets/pages/img/logo-80.png') }}" alt="Logo GPIB Siloam Pontianak" width="60"
                height="60" class="d-inline-block align-middle me-2">
            <span class="d-none d-sm-inline">GPIB SILOAM PONTIANAK</span>
            <span class="d-inline d-sm-none">GPIB SILOAM</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-lg-center">
                {{-- Navigasi Utama --}}
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('beranda') ? 'active' : '' }}" href="{{ route('beranda') }}"
                        @if (Route::is('beranda')) aria-current="page" @endif>Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('jadwal-ibadah*') ? 'active' : '' }}"
                        href="{{ route('jadwal-ibadah') }}"
                        @if (Route::is('jadwal-ibadah*')) aria-current="page" @endif>Jadwal Ibadah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('renungan*') ? 'active' : '' }}" href="{{ route('renungan') }}"
                        @if (Route::is('renungan*')) aria-current="page" @endif>Renungan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('warta-jemaat*') ? 'active' : '' }}" href="{{ route('warta-jemaat') }}"
                        @if (Route::is('warta-jemaat*')) aria-current="page" @endif>Warta Jemaat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('tata-ibadah*') ? 'active' : '' }}" href="{{ route('tata-ibadah') }}"
                        @if (Route::is('tata-ibadah*')) aria-current="page" @endif>Tata Ibadah</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Route::is('info*') ? 'active' : '' }}" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Tentang Gereja
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ Route::is('info') ? 'active' : '' }}"
                                href="{{ route('info') }}">Info</a></li>
                    </ul>
                </li>

                @auth('web')
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle {{ Route::is('profil*') ? 'active' : '' }}" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item {{ Route::is('profil') ? 'active' : '' }}"
                                    href="{{ route('profil') }}">Profil Saya</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('jemaat_logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item"
                                        style="border: none; background: none; width: 100%; text-align: left; padding: var(--bs-dropdown-item-padding-y) var(--bs-dropdown-item-padding-x); color: var(--bs-dropdown-link-color);">
                                        <i class="fas fa-sign-out-alt me-1"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    {{-- Tombol Masuk --}}
                    <li class="nav-item ms-lg-2 mb-2 mb-lg-0">
                        <a class="btn btn-outline-primary btn-sm w-100 w-lg-auto {{ Route::is('pages.login') ? 'active' : '' }}"
                            href="{{ route('pages.login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mb-2 mb-lg-0">
                        <a class="btn btn-primary btn-sm w-100 w-lg-auto {{ Route::is('pages.register') ? 'active' : '' }}"
                            href="{{ route('pages.register') }}">
                            <i class="fas fa-user-plus me-1"></i> Daftar
                        </a>
                    </li>
                @endauth

                <li class="nav-item ms-lg-2 @guest mb-2 mb-lg-0 @endguest">
                    <button id="btnToggleSpeech" class="btn btn-success btn-sm text-white w-100 w-lg-auto"
                        ype="button" title="Aktifkan/Nonaktifkan Pembaca Teks">
                        <i class="fas fa-volume-up"></i>
                        <span class="d-lg-none ms-1">Pembaca Teks</span>
                    </button>
                </li>

            </ul>
        </div>
    </div>
</nav>
