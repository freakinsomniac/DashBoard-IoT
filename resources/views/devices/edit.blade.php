@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow rounded-4 border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-primary" style="letter-spacing:1px;">
                        <i class="bi bi-hdd-network"></i> Edit Perangkat & Sensor
                    </h3>
                    <form method="POST" action="{{ route('devices.update', $device->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Perangkat</label>
                            <input type="text" name="name" id="name" class="form-control shadow-sm @error('name') is-invalid @enderror" value="{{ old('name', $device->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe Board</label>
                            <input type="text" name="type" id="type" class="form-control shadow-sm @error('type') is-invalid @enderror" value="{{ old('type', $device->type) }}">
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select shadow-sm @error('status') is-invalid @enderror">
                                <option value="online" {{ old('status', $device->status) == 'online' ? 'selected' : '' }}>ONLINE</option>
                                <option value="offline" {{ old('status', $device->status) == 'offline' ? 'selected' : '' }}>OFFLINE</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <input type="text" name="description" id="description" class="form-control shadow-sm @error('description') is-invalid @enderror" value="{{ old('description', $device->description) }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status MQTT --}}
                        <div class="mb-3">
                            <label class="form-label">Status MQTT</label>
                            <div>
                                @if(strtolower($device->status) == 'online')
                                    <span class="badge bg-success px-3 py-2" style="font-size:1em;">ONLINE</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2" style="font-size:1em;">OFFLINE</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Simpan Perubahan
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
@endsection