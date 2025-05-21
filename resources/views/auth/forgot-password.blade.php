@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(to right, #74ebd5, #ACB6E5);
    }

    .forgot-container {
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

<div class="container forgot-container d-flex justify-content-center align-items-center">
    <div class="col-md-6">
        <div class="card p-4">
            <div class="card-body">
                <h3 class="text-center mb-4">Lupa Password</h3>

                @if (session('status'))
                    <div class="alert alert-success mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Kirim Link Reset Password
                        </button>
                    </div>
                </form>

                <hr>

                <div class="text-center">
                    <a href="{{ route('login') }}">Kembali ke halaman login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
