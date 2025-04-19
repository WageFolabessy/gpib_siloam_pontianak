@extends('pages.auth.auth')

@section('auth-title', 'Reset Password')

@section('auth-content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope fa-fw"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email', $email ?? request()->query('email')) }}" required
                    autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" placeholder="Masukkan password baru" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Konfirmasi password baru" required autocomplete="new-password">
            </div>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
        </div>
    </form>
@endsection

@section('auth-footer-links')
    <p class="mb-0 text-muted">Ingat password? <a href="{{ route('pages.login') }}">Kembali ke Login</a></p>
@endsection
