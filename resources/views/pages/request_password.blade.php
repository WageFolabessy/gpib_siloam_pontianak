<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>GPIB SILOAM PONTIANAK - Request Password</title>
    <link rel="shortcut icon" href="{{ asset('assets/pages/img/logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/pages/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/pages/css/auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/vendor/fontawesome-free/css/all.min.css') }}">
</head>

<body class="bg-dark-subtle">
    <div class="tengah">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-lg-6">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-dark-subtle text-center">
                            <img src="{{ asset('assets/pages/img/logo.png') }}" alt="Logo" class="logo" />
                            <h1 class="mt-3 text-dark">GPIB SILOAM PONTIANAK</h1>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-success m-3">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <form method="POST" action="{{ route('password.email') }}" class="w-100">
                                @csrf
                                <div class="form-group">
                                    <label for="email" class="sr-only">Email:</label>
                                    <div class="input-group mt-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text h-100">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Masukan email anda yang terdaftar" autocomplete="email" />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-4">
                                    Kirim Link Reset Password
                                </button>
                            </form>
                        </div>
                        <div class="card-footer bg-white text-center">
                            <p class="mb-1">
                                Ingat password? <a href="{{ route('pages.login') }}">Masuk</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
