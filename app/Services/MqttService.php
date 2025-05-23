<?php
namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    public function publish($topic, $message)
    {
        $server   = env('MQTT_HOST', 'test.mosquitto.org');
        $port     = env('MQTT_PORT', 1883);
        $clientId = env('MQTT_CLIENT_ID', 'laravel-client');
        $username = env('MQTT_USERNAME', null);
        $password = env('MQTT_PASSWORD', null);

        if ($username === '' || $username === 'null') $username = null;
        if ($password === '' || $password === 'null') $password = null;

        $settings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setKeepAliveInterval(60); // <-- Tambahkan baris ini

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($settings, true);
        $mqtt->publish($topic, $message, 0);
        $mqtt->disconnect();
    }
}

// Example usage (move this code to a controller or route as needed):
// $mqtt = new MqttService();
// $mqtt->publish('nata/python/mqtt', json_encode([
//     'device_id'    => 99,
//     'value_temp'   => 25,
//     'value_ph'     => 7,
//     'value_height' => 100
// ]));
// return 'Pesan MQTT JSON berhasil dikirim ke test.mosquitto.org pada topic nata/python/mqtt';