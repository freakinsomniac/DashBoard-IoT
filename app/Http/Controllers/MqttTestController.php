<?php

namespace App\Http\Controllers;

use App\Services\MqttService;

class MqttTestController extends Controller
{
    public function publish()
    {
        $mqtt = new MqttService();
        $mqtt->publish('nata/python/mqtt', 'Hello from Laravel!');
        return 'Pesan MQTT berhasil dikirim ke test.mosquitto.org pada topic nata/python/mqtt';
    }
}
