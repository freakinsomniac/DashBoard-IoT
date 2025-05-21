<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SensorDataController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil hanya device milik user login
        $devices = \App\Models\Device::where('user_id', $user->id)->get();
        // Ambil hanya sensor milik device user login
        $sensors = \App\Models\SensorData::whereIn('device_id', $devices->pluck('id'))->get();
        return view('sensor-data.index', compact('devices', 'sensors'));
    }

    public function getLatestData()
    {
        // Ambil data terbaru, misal berdasarkan timestamp
        $latest = \App\Models\SensorData::orderBy('timestamp', 'desc')->first();

        return response()->json([
            'value' => $latest ? $latest->value : 0,
            'timestamp' => $latest ? $latest->timestamp : now(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sensor_id' => 'required',
            'sensor_type' => 'required',
        ]);

        $device = \App\Models\Device::find($request->device_id);

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        \App\Models\SensorData::create([
            'device_id'   => $request->device_id,
            'sensor_id'   => $request->sensor_id,
            'sensor_type' => $request->sensor_type,
            'value'       => $request->value,
            'timestamp'   => now(),
        ]);

        return response()->json(['success' => 'Data created successfully']);
    }
}