<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.index') }}">
        <div class="sidebar-brand-icon">
            <i><img src="{{ asset('assets/dashboard/salib.png') }}" alt="Logo" style="height: 35px; width: auto;"></i>
        </div>
        <div class="sidebar-brand-text mx-3">GPIB SILOAM</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Route::is('dashboard.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}"
            @if (Route::is('dashboard.index')) aria-current="page" @endif>
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Konten</div>

    <li class="nav-item {{ Route::is('dashboard.renungan') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.renungan') }}"
            @if (Route::is('dashboard.renungan')) aria-current="page" @endif>
            <i class="fas fa-fw fa-book-open"></i>
            <span>Renungan</span></a>
    </li>

    <li class="nav-item {{ Route::is('dashboard.jadwal_ibadah') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.jadwal_ibadah') }}"
            @if (Route::is('dashboard.jadwal-ibadah')) aria-current="page" @endif>
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Jadwal Ibadah</span></a>
    </li>

    <li class="nav-item {{ Route::is('dashboard.pendeta') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.pendeta') }}"
            @if (Route::is('dashboard.pendeta')) aria-current="page" @endif>
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Pendeta & Majelis</span></a>
    </li>

    <li class="nav-item {{ Route::is('dashboard.tanya_jawab') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.tanya_jawab') }}"
            @if (Route::is('dashboard.tanya_jawab')) aria-current="page" @endif>
            <i class="fas fa-fw fa-question-circle"></i>
            <span>Tanya Jawab</span></a>
    </li>

    <li class="nav-item {{ Route::is('admin.chat.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.chat.index') }}"
            @if (Route::is('admin.chat.index')) aria-current="page" @endif>
            <i class="fas fa-fw fa-comments"></i>
            <span>Chat</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Pengguna</div>

    <li class="nav-item {{ Route::is('dashboard.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.admin') }}"
            @if (Route::is('dashboard.admin')) aria-current="page" @endif>
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Admin</span></a>
    </li>

    <li class="nav-item {{ Route::is('dashboard.jemaat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.jemaat') }}"
            @if (Route::is('dashboard.jemaat')) aria-current="page" @endif>
            <i class="fas fa-fw fa-users"></i>
            <span>Jemaat</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengakhiri sesi ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('admin.logout') }}" id="logout-form-modal">
                    @csrf
                    <button type="submit" class="btn btn-primary">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>
</div>
