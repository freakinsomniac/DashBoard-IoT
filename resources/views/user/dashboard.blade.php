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
                                            <span id="status-badge-{{ $device->id }}" class="badge {{ $device->status === 'online' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                                {{ ucfirst($device->status) }}
                                            </span>
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
                                    <option value="value_temp">Suhu (&deg;C)</option>
                                    <option value="value_ph">pH</option>
                                    <option value="value_height">Tinggi (cm)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabel Data Sensor -->
            <div class="dashboard-card mt-4">
                <div class="dashboard-title mb-3">
                    <i class="bi bi-thermometer-half"></i> Data Sensor Terbaru
                </div>
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Device</th>
                            <th>Waktu</th>
                            <th>Temp (&deg;C)</th>
                            <th>pH</th>
                            <th>Tinggi (cm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sensorData as $data)
                            <tr>
                                <td>{{ $data->device->name ?? '-' }}</td>
                                <td>{{ $data->timestamp }}</td>
                                <td>
                                    @if(!is_null($data->value_temp))
                                        <span class="badge bg-info">{{ $data->value_temp }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($data->value_ph))
                                        <span class="badge bg-success">{{ $data->value_ph }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($data->value_height))
                                        <span class="badge bg-warning text-dark">{{ $data->value_height }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">Belum ada data sensor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
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
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.update();
    });

    document.getElementById('sensor-input').addEventListener('change', function () {
        selectedSensor = this.value;
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.data.datasets[0].label = this.options[this.selectedIndex].text;
        chart.update();
    });

    // Fetch data realtime
    setInterval(function () {
        if (!selectedDevice || !selectedSensor) return;
        fetch(`/api/sensor-data/latest?device_id=${selectedDevice}`)
            .then(response => response.json())
            .then(data => {
                if (typeof data[selectedSensor] === 'undefined' || data[selectedSensor] === null) return;
                const now = new Date().toLocaleTimeString();
                chart.data.labels.push(now);
                chart.data.datasets[0].data.push(data[selectedSensor]);
                if (chart.data.labels.length > 20) {
                    chart.data.labels.shift();
                    chart.data.datasets[0].data.shift();
                }
                chart.update();
            });
    }, 2000);
});

const mqttUrl = 'wss://test.mosquitto.org:8081/mqtt'; // MQTT over WebSocket
const topic = 'nata/python/mqtt';

const client = mqtt.connect(mqttUrl);

client.on('connect', function () {
    console.log('Connected to MQTT broker');
    client.subscribe(topic, function (err) {
        if (!err) {
            console.log('Subscribed to topic:', topic);
        }
    });
});

client.on('message', function (topic, message) {
    // Misal pesan: {"device_id":1,"value_temp":25,"value_ph":7,"value_height":10}
    try {
        const data = JSON.parse(message.toString());
        // Ambil device dan sensor yang dipilih user
        const selectedDevice = document.getElementById('device-input').value;
        const selectedSensor = document.getElementById('sensor-input').value;

        if (data.device_id == selectedDevice && selectedSensor) {
            const now = new Date().toLocaleTimeString();
            chart.data.labels.push(now);
            chart.data.datasets[0].data.push(data[selectedSensor]);
            if (chart.data.labels.length > 20) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
            }
            chart.update();
        }
    } catch (e) {
        console.error('Invalid MQTT message:', message.toString());
    }
});

function fetchDeviceStatus() {
    fetch('/api/devices/status')
        .then(res => res.json())
        .then(devices => {
            devices.forEach(device => {
                const badge = document.getElementById('status-badge-' + device.id);
                if (badge) {
                    if (device.status === 'online') {
                        badge.className = 'badge bg-success px-3 py-2';
                        badge.textContent = 'Online';
                    } else {
                        badge.className = 'badge bg-secondary px-3 py-2';
                        badge.textContent = 'Offline';
                    }
                }
            });
        });
}
setInterval(fetchDeviceStatus, 3000); // refresh setiap 3 detik
</script>
@endsection
