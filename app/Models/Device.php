<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\MqttService;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_device',
        'serial_number',
        'lembaga_id',
        'user_id',
        'status',
        'last_connection',
        'keterangan',
        'device_info'
    ];

    protected $casts = [
        'last_connection' => 'datetime',
        'device_info' => 'array'
    ];

    /**
     * Relationship dengan Lembaga
     */
    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }

    /**
     * Relationship dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if device is online
     */
    public function isOnline(): bool
    {
        if (!$this->last_connection) {
            return false;
        }
        
        // Consider device online if last connection is within 5 minutes
        return $this->last_connection->diffInMinutes(now()) <= 5;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'success',
            'tidak_aktif' => 'danger',
            'maintenance' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get connection status
     */
    public function getConnectionStatusAttribute(): string
    {
        if ($this->status !== 'aktif') {
            return 'offline';
        }
        
        return $this->isOnline() ? 'online' : 'offline';
    }

    /**
     * Send command to device via MQTT
     */
    public function sendCommand(array $command): bool
    {
        $mqttService = app(MqttService::class);
        return $mqttService->sendDeviceCommand($this->serial_number, $command);
    }

    /**
     * Send material to device
     */
    public function sendMaterial(array $materialData): bool
    {
        $mqttService = app(MqttService::class);
        return $mqttService->sendMaterial($this->serial_number, $materialData);
    }

    /**
     * Ping device
     */
    public function ping(): bool
    {
        $mqttService = app(MqttService::class);
        return $mqttService->pingDevice($this->serial_number);
    }

    /**
     * Update last connection timestamp
     */
    public function updateLastConnection(): void
    {
        $this->update(['last_connection' => now()]);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan lembaga
     */
    public function scopeWithLembaga($query, $lembagaId)
    {
        return $query->where('lembaga_id', $lembagaId);
    }

    /**
     * Scope untuk device yang online
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'aktif')
                    ->where('last_connection', '>=', now()->subMinutes(5));
    }

    /**
     * Generate serial number
     */
    public static function generateSerialNumber(): string
    {
        do {
            $serial = 'EDU' . strtoupper(str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT));
        } while (self::where('serial_number', $serial)->exists());
        
        return $serial;
    }
}