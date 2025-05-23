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

        // Query histories sesuai filter
        $query = SensorData::with('device')
            ->whereIn('device_id', $devices->pluck('id'))
            ->when($request->filled('device_id'), function ($q) use ($request) {
                $q->where('device_id', $request->device_id);
            })
            ->orderByDesc('timestamp');
        $histories = $query->paginate(20);

        // Kirim ke view
        return view('history.index', compact('devices', 'histories'));
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

        // Tidak perlu filter sensor_id lagi

        $histories = $query->limit(50)->get();

        return response()->json($histories);
    }
}