<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>GPIB SILOAM PONTIANAK - Login</title>
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
                        @if ($errors->has('message'))
                            <div class="alert alert-danger m-3">
                                {{ $errors->first('message') }}
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <form method="POST" action="{{ url('/login') }}" class="w-100">
                                @csrf
                                <div class="form-group">
                                    <label for="username" class="sr-only">Username:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text h-100">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="username" name="username"
                                            placeholder="Enter your username" autocomplete="username" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="sr-only">Password:</label>
                                    <div class="input-group mt-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text h-100">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        </div>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter your password" autocomplete="current-password" />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-4">
                                    Login
                                </button>
                            </form>
                        </div>
                        <div class="card-footer bg-white text-center">
                            <p class="mb-1">Admin Dashboard</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
