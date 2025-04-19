@extends('pages.auth.auth')

@section('auth-title', 'Register Jemaat Baru')

@section('auth-content')
    <form method="POST" action="{{ route('register_jemaat') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user fa-fw"></i></span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    placeholder="Masukkan nama lengkap Anda" value="{{ old('name') }}" required autocomplete="name"
                    autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope fa-fw"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" placeholder="Masukkan email Anda" value="{{ old('email') }}" required
                    autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Ulangi password" required autocomplete="new-password">
            </div>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Daftar</button>
        </div>
    </form>
@endsection

@section('auth-footer-links')
    <p class="mb-1 text-muted">Sudah punya akun? <a href="{{ route('pages.login') }}">Masuk</a></p>
@endsection
