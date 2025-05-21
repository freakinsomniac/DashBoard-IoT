@extends('layouts.guest')

@section('content')
<style>
    body {
        background: linear-gradient(to right, #74ebd5, #ACB6E5);
    }

    .register-container {
        min-height: 80vh;
    }

    .card {
        border-radius: 20px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background-color: #4e73df;
        border: none;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
    }
</style>

<div class="container register-container d-flex justify-content-center align-items-center">
    <div class="col-md-6">
        <div class="card p-4">
            <div class="card-body">
                <h3 class="text-center mb-4">Daftar Akun Baru</h3>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" required>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" type="password"
                               class="form-control"
                               name="password_confirmation" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Daftar
                        </button>
                    </div>
                </form>

                <hr>

                <div class="text-center">
                    <a href="{{ route('login') }}">Sudah punya akun? Masuk</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
