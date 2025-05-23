<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\SensorData;

class MqttListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to MQTT topic and save sensor data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        while (true) {
            try {
                $mqtt = new \PhpMqtt\Client\MqttClient(
                    env('MQTT_HOST', 'test.mosquitto.org'),
                    env('MQTT_PORT', 1883),
                    'laravel-listen'
                );
                $settings = (new \PhpMqtt\Client\ConnectionSettings)
                    ->setKeepAliveInterval(60);

                $mqtt->connect($settings, true);

                $mqtt->subscribe('nata/python/mqtt', function ($topic, $message) {
                    $data = json_decode($message, true);
                    if ($data && isset($data['device_id'])) {
                        \App\Models\SensorData::create([
                            'device_id'    => $data['device_id'],
                            'value_temp'   => $data['value_temp'] ?? null,
                            'value_ph'     => $data['value_ph'] ?? null,
                            'value_height' => $data['value_height'] ?? null,
                            'timestamp'    => now(),
                        ]);
                        // Update status device jadi online
                        \App\Models\Device::where('id', $data['device_id'])->update(['status' => 'online']);
                    }
                }, 0);

                $mqtt->subscribe('device/+/status', function ($topic, $message) {
                    // Ambil device_id dari topic, misal: device/16/status
                    preg_match('/device\/(\d+)\/status/', $topic, $matches);
                    $deviceId = $matches[1] ?? null;
                    if ($deviceId) {
                        \App\Models\Device::where('id', $deviceId)->update([
                            'status' => trim($message), // pastikan kolom status ada di tabel devices
                        ]);
                    }
                }, 0);

                $mqtt->loop(true);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                sleep(5);
            }
        }
    }
}
