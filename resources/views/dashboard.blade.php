@extends('layouts.app')

@section('content')
<div class="container-fluid" style="min-height: 100vh;">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-light border-end" style="min-height: 100vh;">
            <nav class="nav flex-column py-4">
                <a class="nav-link fw-bold active" href="#">Home</a>
                <a class="nav-link" href="#">Board</a>
                <a class="nav-link" href="#">History</a>
                <a class="nav-link" href="#">Settings</a>
            </nav>
        </div>
        <!-- Main Content -->
        <div class="col-md-10 py-4">
            <div class="row">
                <!-- Device Table -->
                <div class="col-md-6">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th colspan="3">Daftar Device</th>
                            </tr>
                            <tr>
                                <th>Nama Device</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data device akan diisi dari database --}}
                        </tbody>
                    </table>
                </div>
                <!-- Grafik dan Input -->
                <div class="col-md-6 d-flex flex-column align-items-center">
                    <div class="mb-4" style="width: 100%; max-width: 400px;">
                        <canvas id="sensorChart" height="200"></canvas>
                        <div class="text-center mt-2"></div>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <div>
                            <label for="device-input" class="form-label">Pilih Device</label>
                            <select id="device-input" class="form-select">
                                <option value="">Pilih Device</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
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
