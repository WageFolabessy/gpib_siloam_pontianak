<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.index') }}">
        <div class="sidebar-brand-icon">
            <i><img src="{{ asset('assets/dashboard/salib.png') }}" alt=""></i>
        </div>
        <div class="sidebar-brand-text mx-3">GPIB SILOAM PONTIANAK</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider" />

    <!-- Heading -->
    <div class="sidebar-heading">Content</div>

    <!-- Nav Item Renungan-->
    <li class="nav-item {{ request()->is('dashboard/renungan') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.renungan') }}">
            <i class="fas fa-book"></i>
            <span>Renungan</span></a>
    </li>
    <!-- Nav Item Jadwal Ibadah-->
    <li class="nav-item {{ request()->is('dashboard/jadwal_ibadah') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.jadwal_ibadah') }}">
            <i class="fas fa-calendar"></i>
            <span>Jadwal Ibadah</span></a>
    </li>
    <!-- Nav Item Pendeta, Majelis-->
    <li class="nav-item {{ request()->is('dashboard/pendeta') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.pendeta') }}">
            <i class="fas fa-bible"></i>
            <span>Pendeta</span></a>
    </li>
    <!-- Nav Item Template Tanya Jawab-->
    <li class="nav-item {{ request()->is('dashboard/tanya_jawab') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.tanya_jawab') }}">
            <i class="fas fa-question"></i>
            <span>Template Tanya Jawab</span></a>
    </li>
    <!-- Nav Item Chat-->
    <li class="nav-item {{ request()->is('dashboard/chat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.chat') }}">
            <i class="fas fa-comment"></i>
            <span>Chat</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block" />
    <div class="sidebar-heading">User</div>
    <!-- Nav Item  User-->
    <li class="nav-item {{ request()->is('dashboard/admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.admin') }}">
            <i class="fas fa-user"></i>
            <span>Admin</span></a>
    </li>
    <li class="nav-item {{ request()->is('dashboard/jemaat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.jemaat') }}">
            <i class="fas fa-user"></i>
            <span>Jemaat</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block" />
    <div class="sidebar-heading">Logout</div>
    <!-- Nav Item -->
    <li class="nav-item">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="border: none; background: none; cursor: pointer;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </button>
        </form>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block" />

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
