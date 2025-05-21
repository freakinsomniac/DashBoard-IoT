@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-center w-100" style="margin-left:-100px; color:#2563eb; letter-spacing:2px; font-weight:700;">
                    <i class="bi bi-hdd-network"></i> DAFTAR BOARD
                </h3>
                <a href="{{ route('devices.create') }}" class="btn btn-success shadow-sm" style="height:40px;">
                    <i class="bi bi-plus-circle"></i>
                </a>
            </div>
            <div class="card shadow-sm rounded-4">
                <div class="card-body p-0">
                    <table class="table table-bordered text-center align-middle mb-0" style="background:#fff;">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Tipe Board</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $device)
                                <tr>
                                    <td style="font-weight:500;">{{ $device->name }}</td>
                                    <td>{{ $device->type }}</td>
                                    <td>
                                        @if(strtolower($device->status) == 'online')
                                            <span class="badge bg-success px-3 py-2" style="font-size:1em;">ONLINE</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2" style="font-size:1em;">OFFLINE</span>
                                        @endif
                                    </td>
                                    <td>{{ $device->description ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted">Belum ada perangkat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection