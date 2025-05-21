<?php

// UserDashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    // Dashboard: tampilkan device & sensor milik user login
    public function index()
    {
        $user = Auth::user();
        $devices = Device::where('user_id', $user->id)->get();
        $sensors = SensorData::whereIn('device_id', $devices->pluck('id'))->get();

        return view('user.dashboard', compact('devices', 'sensors'));
    }

    // Form tambah device
    public function createDevice()
    {
        return view('user.create_device');
    }

    // Proses tambah device
    public function storeDevice(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        Device::create([
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Device berhasil ditambahkan!');
    }

    // Form tambah sensor untuk device tertentu
    public function createSensor($device_id)
    {
        $device = Device::where('id', $device_id)->where('user_id', Auth::id())->firstOrFail();
        return view('user.create_sensor', compact('device'));
    }

    // Proses tambah sensor
    public function storeSensor(Request $request, $device_id)
    {
        $request->validate([
            'sensor_id' => 'required',
            'sensor_type' => 'required',
        ]);

        $device = Device::where('id', $device_id)->where('user_id', Auth::id())->firstOrFail();

        SensorData::create([
            'device_id' => $device->id,
            'sensor_id' => $request->sensor_id,
            'sensor_type' => $request->sensor_type,
            'value' => 0,
            'timestamp' => now(),
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Sensor berhasil ditambahkan!');
    }
}
