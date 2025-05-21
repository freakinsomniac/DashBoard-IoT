<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Ambil semua device milik user
        $devices = Device::where('user_id', $user->id)->get();

        // Ambil semua sensor milik device user
        $sensors = SensorData::whereIn('device_id', $devices->pluck('id'))
            ->select('sensor_id', 'sensor_type', 'device_id')
            ->distinct()
            ->get();

        // Query histories sesuai filter
        $query = SensorData::whereIn('device_id', $devices->pluck('id'))->with('device')->orderByDesc('timestamp');
        if ($request->filled('sensor_id')) {
            $query->where('sensor_id', $request->sensor_id);
        }
        $histories = $query->paginate(20);

        // Kirim ke view
        return view('history.index', compact('devices', 'sensors', 'histories'));
    }

    public function apiHistory(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $devices = Device::where('user_id', $user->id)->pluck('id');
        $query = SensorData::whereIn('device_id', $devices)
            ->with('device')
            ->orderByDesc('timestamp');

        if ($request->filled('sensor_id')) {
            $query->where('sensor_id', $request->sensor_id);
        }

        $histories = $query->limit(50)->get();

        return response()->json($histories);
    }
}