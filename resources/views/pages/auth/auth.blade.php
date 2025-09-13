<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SISTEM INFORMASI GEREJA @yield('auth-title')</title>
    <link rel="shortcut icon" href="{{ asset('assets/pages/img/logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/pages/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/pages/css/auth.css') }}" />
</head>

<body class="bg-light">
    <div class="tengah d-flex align-items-center justify-content-center min-vh-100">
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-10 col-12">
                    <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                        <div class="card-header bg-white text-center border-0 py-4">
                            <img src="{{ asset('assets/pages/img/logo.png') }}" alt="Logo Sistem Informasi Gereja"
                                class="mb-3" style="max-height: 70px;" />
                            <h3 class="mb-0 text-dark fw-light">SISTEM INFORMASI GEREJA</h3>
                            <h4 class="text-muted fw-light mt-1">@yield('auth-title')</h4>
                        </div>

                        <div class="px-3 px-md-4 pt-3">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                        </div>

                        <div class="card-body p-4 p-md-5">
                            @yield('auth-content')
                        </div>

                        <div class="card-footer bg-white text-center py-3 border-0">
                            @yield('auth-footer-links')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/pages/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
