@extends('pages.auth.auth')

@section('auth-title', 'Login Jemaat')

@section('auth-content')
    <form method="POST" action="{{ route('login_jemaat') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope fa-fw"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" placeholder="Masukkan email Anda"
                       value="{{ old('email') }}" required autocomplete="email" autofocus>
                 @error('email')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                 <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
                 <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" placeholder="Masukkan password"
                        required autocomplete="current-password">
                  @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
            </div>
        </div>
         <div class="mb-3 form-check">
             <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
             <label class="form-check-label" for="remember">Ingat Saya</label>
         </div>
        <div class="d-grid">
             <button type="submit" class="btn btn-primary btn-lg">Login</button>
        </div>
    </form>
@endsection

@section('auth-footer-links')
    <p class="mb-1 text-muted"><a href="{{ route('password.request') }}">Lupa password?</a></p> {{-- Ganti route name --}}
    <p class="mb-0 text-muted">Belum punya akun? <a href="{{ route('pages.register') }}">Daftar Sekarang</a></p>
@endsection