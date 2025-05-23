@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow rounded-4 border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-primary" style="letter-spacing:1px;">
                        <i class="bi bi-hdd-network"></i> Tambah Perangkat & Sensor
                    </h3>
                    <form method="POST" action="{{ route('devices.store') }}">
                        @csrf

                        {{-- Pilih device lama --}}
                        <div class="mb-4">
                            <label for="device_id" class="form-label fw-semibold text-secondary">Pilih Perangkat yang Sudah Ada <span class="text-muted">(opsional)</span></label>
                            <select name="device_id" id="device_id" class="form-select @error('device_id') is-invalid @enderror">
                                <option value="">-- Pilih Perangkat --</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                        {{ $device->name }} ({{ $device->type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Input device baru --}}
                        <div id="device-new-fields">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Perangkat Baru</label>
                                <input type="text" name="name" id="name" class="form-control shadow-sm @error('name') is-invalid @enderror" value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe Board Baru</label>
                                <input type="text" name="type" id="type" class="form-control shadow-sm @error('type') is-invalid @enderror" value="{{ old('type') }}">
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select shadow-sm @error('status') is-invalid @enderror">
                                    <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>ONLINE</option>
                                    <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>OFFLINE</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Keterangan</label>
                                <input type="text" name="description" id="description" class="form-control shadow-sm @error('description') is-invalid @enderror" value="{{ old('description') }}">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Input data sensor baru --}}
                        <hr class="my-4">
                        <h5 class="mb-3 text-primary"><i class="bi bi-activity"></i> Input Data Sensor</h5>
                        <div class="mb-3">
                            <label for="value_temp" class="form-label">Nilai Suhu (&deg;C)</label>
                            <input type="number" step="any" name="value_temp" id="value_temp" class="form-control shadow-sm @error('value_temp') is-invalid @enderror" value="{{ old('value_temp') }}">
                            @error('value_temp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="value_ph" class="form-label">Nilai pH</label>
                            <input type="number" step="any" name="value_ph" id="value_ph" class="form-control shadow-sm @error('value_ph') is-invalid @enderror" value="{{ old('value_ph') }}">
                            @error('value_ph')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="value_height" class="form-label">Nilai Tinggi (cm)</label>
                            <input type="number" step="any" name="value_height" id="value_height" class="form-control shadow-sm @error('value_height') is-invalid @enderror" value="{{ old('value_height') }}">
                            @error('value_height')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="button" id="check-mqtt-btn" class="btn btn-info">
                                <i class="bi bi-wifi"></i> Cek Koneksi MQTT
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('devices.index') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div id="mqtt-status" class="mt-2"></div>
                    </form>

                    {{-- ...existing code... --}}

                    <script>
                    function toggleDeviceNewFields() {
                        const deviceNewFields = document.getElementById('device-new-fields');
                        const deviceId = document.getElementById('device_id').value;
                        if (deviceId) {
                            deviceNewFields.style.display = 'none';
                            document.getElementById('name').value = '';
                            document.getElementById('type').value = '';
                            document.getElementById('status').value = 'online';
                            document.getElementById('description').value = '';
                        } else {
                            deviceNewFields.style.display = '';
                        }
                    }

                    document.getElementById('device_id').addEventListener('change', toggleDeviceNewFields);

                    document.addEventListener('DOMContentLoaded', function() {
                        toggleDeviceNewFields();
                    });

                    document.getElementById('check-mqtt-btn').onclick = function() {
                        const statusDiv = document.getElementById('mqtt-status');
                        statusDiv.innerHTML = '<span class="text-secondary">Mengecek koneksi...</span>';
                        fetch("{{ route('devices.check-mqtt') }}")
                            .then(res => res.json())
                            .then(data => {
                                if(data.status === 'ok') {
                                    statusDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Broker MQTT Tersambung!</span>';
                                } else {
                                    statusDiv.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal konek: ' + data.message + '</span>';
                                }
                            })
                            .catch(err => {
                                statusDiv.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal konek ke broker MQTT.</span>';
                            });
                    };
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection