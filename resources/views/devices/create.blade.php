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
                            <select name="device_id" id="device_id" class="form-select">
                                <option value="">-- Pilih Perangkat --</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->type }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Input device baru --}}
                        <div id="device-new-fields">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Perangkat Baru</label>
                                <input type="text" name="name" id="name" class="form-control shadow-sm">
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe Board Baru</label>
                                <input type="text" name="type" id="type" class="form-control shadow-sm">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select shadow-sm">
                                    <option value="online">ONLINE</option>
                                    <option value="offline">OFFLINE</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Keterangan</label>
                                <input type="text" name="description" id="description" class="form-control shadow-sm">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3 text-primary"><i class="bi bi-activity"></i> Tambah Sensor</h5>
                        <div class="mb-3">
                            <label for="sensor_type" class="form-label">Tipe Sensor</label>
                            <input type="text" name="sensor_type" id="sensor_type" class="form-control shadow-sm" required>
                        </div>
                        <div class="mb-3">
                            <label for="sensor_id" class="form-label">ID Sensor</label>
                            <input type="text" name="sensor_id" id="sensor_id" class="form-control shadow-sm" required>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('devices.index') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('device_id').addEventListener('change', function() {
    const deviceNewFields = document.getElementById('device-new-fields');
    if (this.value) {
        deviceNewFields.style.display = 'none';
        // Kosongkan input device baru jika memilih device lama
        document.getElementById('name').value = '';
        document.getElementById('type').value = '';
        document.getElementById('status').value = 'online';
        document.getElementById('description').value = '';
    } else {
        deviceNewFields.style.display = '';
    }
});
</script>
@endsection