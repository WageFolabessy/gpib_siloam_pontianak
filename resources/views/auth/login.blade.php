<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>GPIB SILOAM PONTIANAK - Admin Login</title>
    <link rel="shortcut icon" href="{{ asset('assets/pages/img/logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/pages/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/pages/css/auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/vendor/fontawesome-free/css/all.min.css') }}">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo {
             max-height: 60px;
             width: auto;
        }
    </style>
</head>

<body class="bg-dark-subtle">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-light text-center py-4 border-0">
                        <img src="{{ asset('assets/pages/img/logo.png') }}" alt="Logo GPIB Siloam Pontianak" class="logo mb-2" />
                        <h1 class="h4 text-dark fw-bold mb-0">ADMIN LOGIN<br>GPIB SILOAM PONTIANAK</h1>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        @error('auth')
                        <div class="alert alert-danger text-center mb-4" role="alert">
                            {{ $message }}
                        </div>
                        @enderror

                        <form method="POST" action="{{ route('admin.login.submit') }}" class="w-100" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label for="username" class="visually-hidden">Username</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrependUser">
                                        <i class="fas fa-user fa-fw"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control @error('username') is-invalid @enderror"
                                           id="username"
                                           name="username"
                                           placeholder="Username"
                                           value="{{ old('username') }}"
                                           required
                                           autocomplete="username"
                                           aria-describedby="inputGroupPrependUser username-error">
                                    @error('username')
                                    <div id="username-error" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="visually-hidden">Password</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrependPass">
                                        <i class="fas fa-lock fa-fw"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Password"
                                           required
                                           autocomplete="current-password"
                                           aria-describedby="inputGroupPrependPass password-error">
                                     @error('password')
                                    <div id="password-error" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                 <input type="checkbox" class="form-check-input" name="remember" id="remember" value="1">
                                 <label class="form-check-label" for="remember">
                                     Ingat Saya
                                 </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3 border-0">
                         <small class="text-muted">GPIB Siloam Pontianak &copy; {{ date('Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>