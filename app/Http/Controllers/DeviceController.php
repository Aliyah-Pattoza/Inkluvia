<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Lembaga;
use App\Models\User;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    protected $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function index(Request $request)
    {
        $query = Device::with(['lembaga', 'user']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_device', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('lembaga', function($lq) use ($search) {
                      $lq->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by lembaga
        if ($request->has('lembaga') && $request->lembaga) {
            $query->where('lembaga_id', $request->lembaga);
        }
        
        // Filter by connection status
        if ($request->has('connection') && $request->connection) {
            if ($request->connection === 'online') {
                $query->online();
            } elseif ($request->connection === 'offline') {
                $query->where(function($q) {
                    $q->where('status', '!=', 'aktif')
                      ->orWhere('last_connection', '<', now()->subMinutes(5))
                      ->orWhereNull('last_connection');
                });
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $devices = $query->paginate(10)->withQueryString();
        $lembagas = Lembaga::all();
        
        // Statistics
        $stats = [
            'total_devices' => Device::count(),
            'online_devices' => Device::online()->count(),
            'active_devices' => Device::where('status', 'aktif')->count(),
            'maintenance_devices' => Device::where('status', 'maintenance')->count(),
        ];
        
        return view('admin.manajemen-perangkat.index', compact('devices', 'lembagas', 'stats'));
    }
    
    public function create()
    {
        $lembagas = Lembaga::all();
        $users = User::where('role', 'user')->get();
        return view('admin.manajemen-perangkat.create', compact('lembagas', 'users'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_device' => 'required|string|max:255',
            'serial_number' => 'nullable|string|unique:devices,serial_number',
            'lembaga_id' => 'required|exists:lembagas,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:aktif,tidak_aktif,maintenance',
            'keterangan' => 'nullable|string|max:1000'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Generate serial number if not provided
        $serialNumber = $request->serial_number ?: Device::generateSerialNumber();
        
        // Validate user_id based on lembaga
        $lembaga = Lembaga::find($request->lembaga_id);
        if ($lembaga->type === 'Individu' && !$request->user_id) {
            return redirect()->back()
                ->withErrors(['user_id' => 'User harus dipilih untuk lembaga individu'])
                ->withInput();
        }
        
        $device = Device::create([
            'nama_device' => $request->nama_device,
            'serial_number' => $serialNumber,
            'lembaga_id' => $request->lembaga_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);
        
        // Send initial command to device if active (optional - don't fail if MQTT fails)
        if ($device->status === 'aktif') {
            try {
                $this->mqttService->sendDeviceCommand($device->serial_number, [
                    'type' => 'device_registered',
                    'device_id' => $device->id,
                    'device_name' => $device->nama_device,
                    'lembaga' => $device->lembaga->nama,
                    'user' => $device->user->nama_lengkap ?? null,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send MQTT command for new device', [
                    'device_id' => $device->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the creation, just log the warning
            }
        }
        
        return redirect()->route('admin.kelola-perangkat')
            ->with('success', 'Perangkat berhasil ditambahkan!');
    }
    
    public function edit(Device $device)
    {
        $lembagas = Lembaga::all();
        $users = User::where('role', 'user')->get();
        return view('admin.manajemen-perangkat.edit', compact('device', 'lembagas', 'users'));
    }
    
    public function update(Request $request, Device $device)
    {
        $validator = Validator::make($request->all(), [
            'nama_device' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:devices,serial_number,' . $device->id,
            'lembaga_id' => 'required|exists:lembagas,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:aktif,tidak_aktif,maintenance',
            'keterangan' => 'nullable|string|max:1000'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Validate user_id based on lembaga
        $lembaga = Lembaga::find($request->lembaga_id);
        if ($lembaga->type === 'Individu' && !$request->user_id) {
            return redirect()->back()
                ->withErrors(['user_id' => 'User harus dipilih untuk lembaga individu'])
                ->withInput();
        }
        
        $oldStatus = $device->status;
        
        $device->update([
            'nama_device' => $request->nama_device,
            'serial_number' => $request->serial_number,
            'lembaga_id' => $request->lembaga_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);
        
        // Send status update command if status changed (optional)
        if ($oldStatus !== $device->status) {
            try {
                $this->mqttService->sendDeviceCommand($device->serial_number, [
                    'type' => 'status_update',
                    'old_status' => $oldStatus,
                    'new_status' => $device->status,
                    'timestamp' => now()->toISOString()
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send MQTT status update', [
                    'device_id' => $device->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the update, just log the warning
            }
        }
        
        return redirect()->route('admin.kelola-perangkat')
            ->with('success', 'Perangkat berhasil diperbarui!');
    }
    
    public function destroy(Device $device)
    {
        // Send deactivation command before deleting (optional)
        if ($device->status === 'aktif') {
            try {
                $this->mqttService->sendDeviceCommand($device->serial_number, [
                    'type' => 'device_deactivated',
                    'message' => 'Device has been removed from system'
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send MQTT deactivation command', [
                    'device_id' => $device->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with deletion even if MQTT fails
            }
        }
        
        $device->delete();
        
        return redirect()->route('admin.kelola-perangkat')
            ->with('success', 'Perangkat berhasil dihapus!');
    }
    
    /**
     * Ping device
     */
    public function ping(Device $device)
    {
        $result = $device->ping();
        
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Ping berhasil dikirim ke perangkat'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim ping ke perangkat'
        ], 500);
    }
    
    /**
     * Request device status
     */
    public function requestStatus(Device $device)
    {
        $command = [
            'type' => 'status_request',
            'timestamp' => now()->toISOString()
        ];
        
        $result = $device->sendCommand($command);
        
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan status berhasil dikirim'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim permintaan status'
        ], 500);
    }
    
    /**
     * Get users by lembaga (AJAX)
     */
    public function getUsersByLembaga(Request $request)
    {
        $lembagaId = $request->get('lembaga_id');
        
        if (!$lembagaId) {
            return response()->json([]);
        }
        
        $lembaga = Lembaga::find($lembagaId);
        
        if ($lembaga->type === 'Individu') {
            // For individual lembaga, return all users
            $users = User::where('role', 'user')
                        ->select('id', 'nama_lengkap', 'email')
                        ->get();
        } else {
            // For institutional lembaga, return users from that lembaga
            $users = User::where('role', 'user')
                        ->where('lembaga_id', $lembagaId)
                        ->select('id', 'nama_lengkap', 'email')
                        ->get();
        }
        
        return response()->json($users);
    }
}