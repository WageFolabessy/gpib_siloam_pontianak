<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="garisAs" content="projek" />
    <title>SISTEM INFORMASI GEREJA @yield('title')</title>
    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/pages/img/logo.png') }}">
    {{-- Style --}}
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    {{-- Icons --}}
    <link href="{{ asset('assets/dashboard/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" />
    {{-- CSS Template Dan Bootstrap v5.3.0 --}}
    <link href="{{ asset('assets/dashboard/vendor/sb-admin-2/sb-admin-2.min.css') }}" rel="stylesheet" />
    <!-- DataTable -->
    <link href="{{ asset('assets/dashboard/vendor/datatables/datatables.min.css') }}" rel="stylesheet" />
    @yield('css')
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        @include('dashboard.components.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content" class="mt-4">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('dashboard.components.footer')
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    {{-- JavaScript Untuk Jquery v3.7.0 dan Bootstrap v5.3.0 --}}
    <script src="{{ asset('assets/dashboard/vendor/jquery/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    {{-- JavaScript Untuk Template Ini --}}
    <script src="{{ asset('assets/dashboard/vendor/sb-admin-2/sb-admin-2.min.js') }}"></script>
    <!-- DataTable -->
    <script src="{{ asset('assets/dashboard/vendor/datatables/datatables.min.js') }}"></script>
    @yield('script')
</body>

</html>
