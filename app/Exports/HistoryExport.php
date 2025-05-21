<?php

namespace App\Exports;

use App\Models\SensorData;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HistoryExport implements FromView
{
    protected $sensor_id;
    protected $user_id;

    public function __construct($sensor_id, $user_id)
    {
        $this->sensor_id = $sensor_id;
        $this->user_id = $user_id;
    }

    public function view(): View
    {
        $devices = \App\Models\Device::where('user_id', $this->user_id)->pluck('id');
        $query = SensorData::whereIn('device_id', $devices)->with('device')->orderByDesc('timestamp');
        if ($this->sensor_id) {
            $query->where('sensor_id', $this->sensor_id);
        }
        $histories = $query->get();

        return view('history.export_excel', compact('histories'));
    }
}
