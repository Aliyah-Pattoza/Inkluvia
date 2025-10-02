<?php

namespace App\Services;

use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class MqttService
{
    private $mqtt;
    private $server;
    private $port;
    private $username;
    private $password;
    private $clientId;

    public function __construct()
    {
        $this->server = config('mqtt.server');
        $this->port = config('mqtt.port');
        $this->username = config('mqtt.username');
        $this->password = config('mqtt.password');
        $this->clientId = config('mqtt.client_prefix') . uniqid();
    }

    /**
     * Connect to MQTT broker
     */
    public function connect(): bool
    {
        // Check if MQTT is enabled
        if (!config('mqtt.enabled', true)) {
            Log::info('MQTT is disabled in configuration');
            return false;
        }

        try {
            $this->mqtt = new phpMQTT($this->server, $this->port, $this->clientId);
            
            $connected = $this->mqtt->connect(
                config('mqtt.clean_session', true),
                null,
                $this->username,
                $this->password
            );

            if ($connected) {
                Log::info('MQTT connected successfully', [
                    'server' => $this->server,
                    'port' => $this->port,
                    'client_id' => $this->clientId
                ]);
                return true;
            }

            Log::error('Failed to connect to MQTT broker');
            return false;

        } catch (\Exception $e) {
            Log::error('MQTT connection error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Publish message to topic
     */
    public function publish(string $topic, $message, int $qos = 0): bool
    {
        try {
            if (!$this->mqtt) {
                if (!$this->connect()) {
                    return false;
                }
            }

            $payload = is_array($message) ? json_encode($message) : (string)$message;
            
            $result = $this->mqtt->publish($topic, $payload, $qos);
            
            // Ensure we always return a boolean
            $success = (bool) $result;
            
            if ($success) {
                Log::info('MQTT message published', [
                    'topic' => $topic,
                    'message' => $payload,
                    'qos' => $qos
                ]);
            } else {
                Log::error('Failed to publish MQTT message', [
                    'topic' => $topic,
                    'message' => $payload
                ]);
            }

            return $success;

        } catch (\Exception $e) {
            Log::error('MQTT publish error: ' . $e->getMessage(), [
                'topic' => $topic,
                'message' => is_array($message) ? json_encode($message) : (string)$message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Subscribe to topic
     */
    public function subscribe(string $topic, callable $callback = null, int $qos = 0): bool
    {
        try {
            if (!$this->mqtt) {
                if (!$this->connect()) {
                    return false;
                }
            }

            $topics = [$topic => ['qos' => $qos, 'function' => $callback]];
            
            $this->mqtt->subscribe($topics, 0);
            
            Log::info('MQTT subscribed to topic', [
                'topic' => $topic,
                'qos' => $qos
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('MQTT subscribe error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send device command
     */
    public function sendDeviceCommand(string $deviceSerial, array $command): bool
    {
        $topic = config('mqtt.topics.device_command') . '/' . $deviceSerial;
        
        $payload = [
            'timestamp' => now()->toISOString(),
            'device_serial' => $deviceSerial,
            'command' => $command,
            'source' => 'inkluvia_web'
        ];

        return $this->publish($topic, $payload);
    }

    /**
     * Send material to device
     */
    public function sendMaterial(string $deviceSerial, array $materialData): bool
    {
        $topic = config('mqtt.topics.material_send') . '/' . $deviceSerial;
        
        $payload = [
            'timestamp' => now()->toISOString(),
            'device_serial' => $deviceSerial,
            'material' => $materialData,
            'source' => 'inkluvia_web'
        ];

        return $this->publish($topic, $payload);
    }

    /**
     * Request device status
     */
    public function requestDeviceStatus(string $deviceSerial): bool
    {
        $command = [
            'type' => 'status_request',
            'timestamp' => now()->toISOString()
        ];

        return $this->sendDeviceCommand($deviceSerial, $command);
    }

    /**
     * Send ping to device
     */
    public function pingDevice(string $deviceSerial): bool
    {
        $command = [
            'type' => 'ping',
            'timestamp' => now()->toISOString()
        ];

        return $this->sendDeviceCommand($deviceSerial, $command);
    }

    /**
     * Disconnect from MQTT broker
     */
    public function disconnect(): void
    {
        if ($this->mqtt) {
            $this->mqtt->close();
            Log::info('MQTT disconnected');
        }
    }

    /**
     * Get connection status
     */
    public function isConnected(): bool
    {
        return $this->mqtt !== null;
    }

    /**
     * Get MQTT configuration
     */
    public function getConfig(): array
    {
        return [
            'server' => $this->server,
            'port' => $this->port,
            'client_id' => $this->clientId,
            'ws_url' => config('mqtt.ws_url'),
            'topics' => config('mqtt.topics')
        ];
    }
}