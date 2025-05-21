@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-primary" style="letter-spacing:1px;">
        <i class="bi bi-clock-history"></i> History Data Perangkat
    </h3>

    <a href="{{ route('history.export', ['sensor_id' => request('sensor_id')]) }}" class="btn btn-success mb-3 shadow-sm">
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
                <label for="sensor_id" class="col-form-label fw-semibold text-secondary">Filter Sensor:</label>
            </div>
            <div class="col-auto">
                <select name="sensor_id" id="sensor_id" class="form-select shadow-sm">
                    <option value="">-- Semua Sensor --</option>
                    @foreach($sensors as $sensor)
                        <option value="{{ $sensor->sensor_id }}" data-device="{{ $sensor->device_id }}" {{ request('sensor_id') == $sensor->sensor_id ? 'selected' : '' }}>
                            {{ $sensor->sensor_type }} (ID: {{ $sensor->sensor_id }})
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
                            <th>Tipe Sensor</th>
                            <th>ID Sensor</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body">
                        <tr>
                            <td colspan="5">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $histories->withQueryString()->links() }}
        </div>
    </div>
</div>

<script>
function renderHistoryTable(histories) {
    let prevValue = null;
    let prevSensor = null;
    let html = '';
    if(histories.length === 0) {
        html = `<tr><td colspan="5" class="text-muted">Belum ada data history.</td></tr>`;
    } else {
        histories.forEach(history => {
            let isChanged = prevSensor === history.sensor_id && prevValue !== null && prevValue != history.value;
            html += `<tr${isChanged ? ' style="background-color: #ffeeba;"' : ''}>
                <td>${history.timestamp}</td>
                <td>${history.device ? history.device.name : '-'}</td>
                <td>${history.sensor_type}</td>
                <td>${history.sensor_id}</td>
                <td>
                    <span class="fw-bold">${history.value}</span>
                    ${isChanged ? '<span class="badge bg-warning text-dark ms-2">Changed</span>' : ''}
                </td>
            </tr>`;
            prevValue = history.value;
            prevSensor = history.sensor_id;
        });
    }
    document.getElementById('history-table-body').innerHTML = html;
}

function fetchHistory() {
    // Ambil value setiap kali fungsi dipanggil
    let deviceId = document.getElementById('device_id').value;
    let sensorId = document.getElementById('sensor_id').value;
    if (!deviceId) {
        document.getElementById('history-message').style.display = '';
        document.getElementById('history-table-wrapper').style.display = 'none';
        return;
    }
    document.getElementById('history-message').style.display = 'none';
    document.getElementById('history-table-wrapper').style.display = '';
    fetch(`/api/history?device_id=${deviceId}&sensor_id=${sensorId}`)
        .then(res => res.json())
        .then(data => renderHistoryTable(data));
}

document.addEventListener('DOMContentLoaded', function() {
    const deviceSelect = document.getElementById('device_id');
    const sensorSelect = document.getElementById('sensor_id');

    function filterSensors() {
        const selectedDevice = deviceSelect.value;
        for (let opt of sensorSelect.options) {
            if (!opt.value) continue;
            if (!selectedDevice || opt.getAttribute('data-device') === selectedDevice) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        }
        // Reset sensor jika tidak cocok
        if (selectedDevice && sensorSelect.selectedOptions.length && sensorSelect.selectedOptions[0].getAttribute('data-device') !== selectedDevice) {
            sensorSelect.value = '';
        }
    }

    deviceSelect.addEventListener('change', function() {
        filterSensors();
        fetchHistory();
    });
    sensorSelect.addEventListener('change', fetchHistory);

    filterSensors(); // initial
    fetchHistory();  // initial
});

// Fetch setiap 3 detik
setInterval(fetchHistory, 3000);
</script>
@endsection