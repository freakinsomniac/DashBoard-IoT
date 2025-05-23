@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow rounded-4 border-0">
                <div class="card-body p-4 text-center">
                    <h4 class="mb-4 text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Hapus Perangkat
                    </h4>
                    <p>Apakah Anda yakin ingin menghapus perangkat berikut?</p>
                    <ul class="list-group mb-3">
                        <li class="list-group-item"><strong>Nama:</strong> {{ $device->name }}</li>
                        <li class="list-group-item"><strong>Tipe:</strong> {{ $device->type }}</li>
                        <li class="list-group-item"><strong>Status:</strong> {{ ucfirst($device->status) }}</li>
                        <li class="list-group-item"><strong>Keterangan:</strong> {{ $device->description ?? '-' }}</li>
                    </ul>
                    <form action="{{ route('devices.destroy', $device->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                        <a href="{{ route('devices.index') }}" class="btn btn-secondary px-4 ms-2">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection