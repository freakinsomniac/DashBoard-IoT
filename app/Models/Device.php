<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['name', 'type', 'user_id'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function sensors()
    {
        return $this->hasMany(\App\Models\SensorData::class, 'device_id');
    }
}

class Sensor extends Model
{
    protected $fillable = ['name', 'device_id'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
