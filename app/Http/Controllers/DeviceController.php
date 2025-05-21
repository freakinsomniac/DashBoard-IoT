<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\SensorData; // gunakan model SensorData

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::with('sensors')->get();
        $sensors = SensorData::all(); // tampilkan semua sensor dari sensor_data
        return view('devices.index', compact('devices', 'sensors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sensor_id' => 'required',
            'sensor_type' => 'required',
        ]);

        // Jika pilih device lama
        if ($request->device_id) {
            $device = \App\Models\Device::where('id', $request->device_id)
                ->where('user_id', optional($request->user())->id)
                ->firstOrFail();
        } else {
            // Jika tambah device baru
            $request->validate([
                'name' => 'required',
                'type' => 'required',
            ]);
            $device = \App\Models\Device::create([
                'name' => $request->name,
                'type' => $request->type,
                'user_id' => optional($request->user())->id,
            ]);
        }

        // Tambah sensor ke device terpilih/baru
        \App\Models\SensorData::create([
            'device_id'   => $device->id,
            'sensor_id'   => $request->sensor_id,
            'sensor_type' => $request->sensor_type,
            'value'       => 0,
            'timestamp'   => now(),
        ]);

        return redirect()->route('devices.index')->with('success', 'Perangkat dan sensor berhasil ditambahkan!');
    }
    public function show($id)
    {
        $device = \App\Models\Device::with('sensors')->findOrFail($id);
        // $device->sensors berisi semua sensor yang terpasang pada device ini
        return view('devices.show', compact('device'));
    }
    public function create(Request $request)
    {
        // Ambil semua device milik user login
        $devices = \App\Models\Device::where('user_id', optional($request->user())->id)->get();
        return view('devices.create', compact('devices'));
    }
}
