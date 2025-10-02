<?php

return [
    'server' => env('MQTT_SERVER', 'broker.hivemq.com'),
    'port' => (int) env('MQTT_PORT', 1883),
    'username' => env('MQTT_USERNAME', ''),
    'password' => env('MQTT_PASSWORD', ''),
    'topic' => env('MQTT_TOPIC', 'abatago/0000'),
    'client_prefix' => env('MQTT_CLIENT_PREFIX', 'phpClient_'),
    'ws_url' => env('MQTT_WS_URL', 'wss://broker.hivemq.com:8884/mqtt'),
    'timeout' => (int) env('MQTT_TIMEOUT', 60),
    'keep_alive' => (int) env('MQTT_KEEP_ALIVE', 60),
    'qos' => (int) env('MQTT_QOS', 0),

    // Connection settings
    'keep_alive' => 60,
    'clean_session' => true,
    'timeout' => 10,
    
    // Topics untuk berbagai fungsi
    'topics' => [
        'device_status' => 'inkluvia/device/status',
        'device_command' => 'inkluvia/device/command',
        'material_send' => 'inkluvia/material/send',
        'device_heartbeat' => 'inkluvia/device/heartbeat',
    ]
];