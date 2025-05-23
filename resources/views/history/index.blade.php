@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-primary" style="letter-spacing:1px;">
        <i class="bi bi-clock-history"></i> History Data Perangkat
    </h3>

    <a href="{{ route('history.export', ['device_id' => request('device_id')]) }}" class="btn btn-success mb-3 shadow-sm">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>

    <form method="GET" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="device_id" class="col-form-label fw-semibold text-secondary">Filter Device:</label>
            </div>
            <div class="col-auto">
                <select name="device_id" id="device_id" class="form-select shadow-sm">
                    <option value="">-- Semua Device --</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                            {{ $device->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm shadow-sm">Tampilkan</button>
            </div>
        </div>
    </form>

    <div id="history-message" class="alert alert-info mt-4" @if(request('device_id')) style="display:none;" @endif>
        <i class="bi bi-info-circle"></i> Silakan pilih device terlebih dahulu untuk melihat histori sensor.
    </div>

    <div id="history-table-wrapper" @if(!request('device_id')) style="display:none;" @endif>
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-0">
                <table class="table table-bordered text-center align-middle mb-0" style="background:#fff;">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Device</th>
                            <th>Suhu (&deg;C)</th>
                            <th>pH</th>
                            <th>Tinggi (cm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td class="text-secondary" style="font-size: 0.95em;">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($history->timestamp)->format('d M Y H:i:s') }}
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        <i class="bi bi-cpu"></i> {{ $history->device ? $history->device->name : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if(!is_null($history->value_temp))
                                        <span class="badge bg-info text-dark px-3 py-2" style="font-size:1em;">
                                            <i class="bi bi-thermometer-half"></i> {{ number_format($history->value_temp, 2) }} &#8451;
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($history->value_ph))
                                        <span class="badge bg-success px-3 py-2" style="font-size:1em;">
                                            <i class="bi bi-droplet"></i> {{ number_format($history->value_ph, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($history->value_height))
                                        <span class="badge bg-warning text-dark px-3 py-2" style="font-size:1em;">
                                            <i class="bi bi-arrow-up"></i> {{ number_format($history->value_height, 0) }} cm
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">Belum ada data history.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    <nav class="d-flex justify-content-center">
                        {{ $histories->withQueryString()->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection