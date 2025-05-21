<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id', 'sensor_id', 'sensor_type', 'value', 'timestamp'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'value' => 'float'
    ];

    public function device()
    {
        return $this->belongsTo(\App\Models\Device::class, 'device_id');
    }
}
// $table->timestamp('timestamp')->nullable();
