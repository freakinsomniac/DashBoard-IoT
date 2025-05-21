@extends('layouts.app')

@section('content')
<style>
    .dashboard-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
        padding: 24px;
        margin-bottom: 24px;
    }
    .dashboard-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2563eb;
        margin-bottom: 18px;
        letter-spacing: 1px;
    }
    .table th, .table td {
        vertical-align: middle !important;
    }
    .form-label {
        font-weight: 500;
        color: #2563eb;
    }
    .form-select, .form-control {
        border-radius: 10px;
    }
    #sensorChart {
        background: #f6f9fc;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.05);
        padding: 10px;
    }
</style>
<div class="container-fluid" style="min-height: 100vh;">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-12 py-4">
            <div class="row">
                <!-- Device Table -->
                <div class="col-md-6">
                    <div class="dashboard-card">
                        <div class="dashboard-title mb-3">
                            <i class="bi bi-hdd-network"></i> Daftar Device
                        </div>
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Device</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devices as $device)
                                    <tr>
                                        <td>{{ $device->name }}</td>
                                        <td>
                                            @if($device->status === 'online')
                                                <span class="badge bg-success px-3 py-2">Online</span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2">Offline</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Belum ada device terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Grafik dan Input -->
                <div class="col-md-6 d-flex flex-column align-items-center">
                    <div class="dashboard-card w-100 d-flex flex-column align-items-center">
                        <div class="dashboard-title mb-2">
                            <i class="bi bi-graph-up"></i> Grafik Sensor
                        </div>
                        <div class="mb-4" style="width: 100%; max-width: 400px;">
                            <canvas id="sensorChart" height="200"></canvas>
                            <div class="text-center mt-2"></div>
                        </div>
                        <div class="d-flex justify-content-center gap-3 w-100">
                            <div class="w-50">
                                <label for="device-input" class="form-label">Pilih Device</label>
                                <select id="device-input" class="form-select">
                                    <option value="">Pilih Device</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-50">
                                <label for="sensor-input" class="form-label">Pilih Sensor</label>
                                <select id="sensor-input" class="form-select">
                                    <option value="">Pilih Sensor</option>
                                    @foreach($sensors as $sensor)
                                        <option value="{{ $sensor->id }}" data-device="{{ $sensor->device_id }}">
                                            {{ $sensor->sensor_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chart;
let selectedDevice = '';
let selectedSensor = '';

document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Chart.js
    const ctx = document.getElementById('sensorChart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Nilai Sensor',
                data: [],
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { display: true, title: { display: true, text: 'Waktu' } },
                y: { display: true, title: { display: true, text: 'Value' } }
            }
        }
    });

    // Filter sensor sesuai device
    document.getElementById('device-input').addEventListener('change', function () {
        selectedDevice = this.value;
        let sensorSelect = document.getElementById('sensor-input');
        for (let opt of sensorSelect.options) {
            if (!opt.value) continue;
            opt.style.display = (opt.getAttribute('data-device') === selectedDevice) ? '' : 'none';
        }
        sensorSelect.value = '';
        selectedSensor = '';
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.update();
    });

    document.getElementById('sensor-input').addEventListener('change', function () {
        selectedSensor = this.value;
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.update();
    });

    // Fetch data realtime
    setInterval(function () {
        if (!selectedDevice || !selectedSensor) return;
        fetch(`/api/sensor-data/latest?device_id=${selectedDevice}&sensor_id=${selectedSensor}`)
            .then(response => response.json())
            .then(data => {
                if (!data.value) return;
                const now = new Date().toLocaleTimeString();
                chart.data.labels.push(now);
                chart.data.datasets[0].data.push(data.value);
                if (chart.data.labels.length > 20) {
                    chart.data.labels.shift();
                    chart.data.datasets[0].data.shift();
                }
                chart.update();
            });
    }, 2000);
});
</script>
@endsection
