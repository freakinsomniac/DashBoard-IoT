<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor_data';

    protected $fillable = [
        'device_id', 'value_temp', 'value_ph', 'value_height', 'timestamp'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public $timestamps = true; // jika pakai created_at/updated_at

    public function device()
    {
        return $this->belongsTo(\App\Models\Device::class, 'device_id');
    }
}
// $table->timestamp('timestamp')->nullable();
