@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4>Grafik Data Sensor Realtime</h4>
    <div id="grafik-area" style="display:none;">
        <canvas id="sensorChart" height="100"></canvas>

        <!-- Indikator digital di bawah grafik -->
        <div class="d-flex flex-column align-items-center mt-4">
            <div id="digital-indicator" style="
                font-family: 'Digital-7', 'Courier New', Courier, monospace;
                font-size: 80px;
                color: #2196f3;
                background: #222;
                border-radius: 12px;
                padding: 20px 40px;
                letter-spacing: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                display: inline-block;
            ">
                0&nbsp;<span style="font-size:32px;">&#8451;</span>
            </div>
            <div class="text-secondary mt-2">Suhu Sensor</div>
        </div>
    </div>

    <!-- Form untuk input kode sensor -->
    <div class="mt-4">
        <input type="text" name="sensor_id" class="form-control" placeholder="Kode Sensor" required>
    </div>

    <!-- Pilih Device dan Sensor -->
    <div class="mb-4">
        <label for="device-select" class="form-label">Pilih Device</label>
        <select id="device-select" class="form-select mb-2">
            <option value="">-- Pilih Device --</option>
            @foreach($devices as $device)
                <option value="{{ $device->id }}">{{ $device->name }}</option>
            @endforeach
        </select>

        <label for="sensor-select" class="form-label">Pilih Sensor</label>
        <select id="sensor-select" class="form-select">
            <option value="">-- Pilih Sensor --</option>
            @foreach($sensors as $sensor)
                <option value="{{ $sensor->id }}" data-device="{{ $sensor->device_id }}">
                    {{ $sensor->sensor_type }}
                </option>
            @endforeach
        </select>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chart;
let dataPoints = [];
let labels = [];

function addData(chart, label, data) {
    chart.data.labels.push(label);
    chart.data.datasets.forEach((dataset) => {
        dataset.data.push(data);
    });
    chart.update();
}

function removeOldestData(chart) {
    chart.data.labels.shift();
    chart.data.datasets.forEach((dataset) => {
        dataset.data.shift();
    });
    chart.update();
}

document.addEventListener('DOMContentLoaded', function () {
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
            scales: {
                x: { title: { display: true, text: 'Waktu' } },
                y: { title: { display: true, text: 'Nilai' }, beginAtZero: true }
            }
        }
    });

    setInterval(function () {
        if (!selectedDevice || !selectedSensor) return;
        fetch("{{ route('sensor.latest') }}?device_id=" + selectedDevice + "&sensor_id=" + selectedSensor)
            .then(response => response.json())
            .then(data => {
                const now = new Date().toLocaleTimeString();
                addData(chart, now, data.value);
                document.getElementById('digital-indicator').innerHTML =
                    data.value + '&nbsp;<span style="font-size:32px;">&#8451;</span>';
                if (chart.data.labels.length > 20) {
                    removeOldestData(chart);
                }
            });
    }, 2000); // polling setiap 2 detik

    let selectedDevice = '';
    let selectedSensor = '';

    document.getElementById('device-select').addEventListener('change', function() {
        selectedDevice = this.value;
        // Filter sensor sesuai device
        let sensorSelect = document.getElementById('sensor-select');
        for (let opt of sensorSelect.options) {
            if (!opt.value) continue;
            opt.style.display = (opt.getAttribute('data-device') === selectedDevice) ? '' : 'none';
        }
        sensorSelect.value = '';
        document.getElementById('grafik-area').style.display = 'none';
    });

    document.getElementById('sensor-select').addEventListener('change', function() {
        selectedSensor = this.value;
        if (selectedDevice && selectedSensor) {
            document.getElementById('grafik-area').style.display = '';
            // Reset grafik jika perlu
            chart.data.labels = [];
            chart.data.datasets[0].data = [];
            chart.update();
        } else {
            document.getElementById('grafik-area').style.display = 'none';
        }
    });
});
</script>
@endpush

<link href="https://fonts.cdnfonts.com/css/digital-7" rel="stylesheet">