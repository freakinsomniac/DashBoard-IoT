@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(to right, #74ebd5, #ACB6E5);
    }

    .verify-container {
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

<div class="container verify-container d-flex justify-content-center align-items-center">
    <div class="col-md-6">
        <div class="card p-4">
            <div class="card-body">
                <h3 class="text-center mb-4">Verifikasi Email</h3>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success mb-3">
                        Link verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                @endif

                <p class="text-center mb-4">
                    Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda? Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkan yang lain.
                </p>

                <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                    @csrf
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-danger">
                            Keluar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
