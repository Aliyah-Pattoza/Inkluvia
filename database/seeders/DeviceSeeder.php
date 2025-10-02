<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Lembaga;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lembagas = Lembaga::all();
        $users = User::where('role', 'user')->get();
        
        $devices = [
            [
                'nama_device' => 'EduBraille Kawan Netra',
                'serial_number' => 'EDU1A2B3C',
                'status' => 'aktif',
                'keterangan' => 'Perangkat untuk Tunanetra Mengaji',
                'last_connection' => now()->subMinutes(2)
            ],
            [
                'nama_device' => 'EduBraille Kawan Netra 2', 
                'serial_number' => 'EDU2D4E6F',
                'status' => 'aktif',
                'keterangan' => 'Perangkat untuk Tunanetra Mengaji',
                'last_connection' => now()->subMinutes(30)
            ]
        ];
        
        foreach ($devices as $index => $deviceData) {
            $lembaga = $lembagas->random();
            $user = null;
            
            // Assign user randomly, more likely for individual lembaga
            if ($lembaga->type === 'Individu' || rand(1, 3) === 1) {
                $user = $users->random();
            }
            
            Device::create([
                'nama_device' => $deviceData['nama_device'],
                'serial_number' => $deviceData['serial_number'],
                'lembaga_id' => $lembaga->id,
                'user_id' => $user?->id,
                'status' => $deviceData['status'],
                'last_connection' => $deviceData['last_connection'],
                'keterangan' => $deviceData['keterangan'],
                'device_info' => [
                    'firmware_version' => '1.' . rand(0, 5) . '.' . rand(0, 9),
                    'hardware_version' => 'v' . rand(1, 3) . '.' . rand(0, 2),
                    'memory_total' => rand(4, 16) . 'GB',
                    'storage_total' => rand(32, 128) . 'GB'
                ]
            ]);
        }
    }
}