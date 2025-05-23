<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\SensorData; // gunakan model SensorData
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

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
                'status' => $request->status,
                'description' => $request->description,
            ]);
        }

        // Tambah data sensor ke device terpilih/baru
        \App\Models\SensorData::create([
            'device_id'    => $device->id,
            'value_temp'   => $request->value_temp,
            'value_ph'     => $request->value_ph,
            'value_height' => $request->value_height,
            'timestamp'    => now(),
        ]);

        return redirect()->route('devices.index')->with('success', 'Perangkat dan data sensor berhasil ditambahkan!');
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

    public function checkMqttBroker()
    {
        try {
            $mqtt = new MqttClient(env('MQTT_HOST', 'test.mosquitto.org'), env('MQTT_PORT', 1883), 'laravel-check');
            $settings = new ConnectionSettings();
            $mqtt->connect($settings, true);
            $mqtt->disconnect();
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $device = \App\Models\Device::findOrFail($id);
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'required|in:online,offline',
            'description' => 'nullable|string|max:255',
        ]);

        $device = \App\Models\Device::findOrFail($id);
        $device->update([
            'name' => $request->name,
            'type' => $request->type,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil diupdate!');
    }
    public function destroy($id)
    {
        $device = \App\Models\Device::findOrFail($id);
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil dihapus!');
    }
}
