@extends('pages.auth.auth')

@section('auth-title', 'Lupa Password')

@section('auth-content')
    <p class="text-center text-muted mb-4">Masukkan alamat email Anda yang terdaftar. Kami akan mengirimkan link untuk
        mereset password Anda.</p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope fa-fw"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                    placeholder="Email terdaftar Anda" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Kirim Link Reset Password</button>
        </div>
    </form>
@endsection

@section('auth-footer-links')
    <p class="mb-0 text-muted">Ingat password? <a href="{{ route('pages.login') }}">Kembali ke Login</a></p>
@endsection
