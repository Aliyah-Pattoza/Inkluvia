<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Device;
use App\Models\Material;
use App\Models\UserSavedMaterial;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    protected $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function index(Request $request)
    {
        $query = Jadwal::where('user_id', Auth::id())->with('user');
        
        // Filter by date
        if ($request->filled('filter_tanggal')) {
            if ($request->filter_tanggal === 'hari_ini') {
                $query->whereDate('tanggal', today());
            }
            // 'semua' = no date filter
        }
        
        // Filter by status
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }
        
        // Sort: upcoming first (by date & time), then completed (newest first)
        $jadwals = $query->get()->sortBy(function($jadwal) {
            if ($jadwal->status === 'selesai') {
                // Completed: sort by date DESC (newest first), add large timestamp
                return $jadwal->tanggal->timestamp + 999999999;
            } else {
                // Active/upcoming: sort by date & time ASC (nearest first)
                $datetime = \Carbon\Carbon::parse($jadwal->tanggal->format('Y-m-d') . ' ' . $jadwal->waktu_mulai);
                return $datetime->timestamp;
            }
        })->values();

        return view('user.jadwal-belajar.index', compact('jadwals'));
    }

    public function create()
    {
        // Get saved materials for the current user
        $savedMaterials = $this->getUserSavedMaterials();
        
        return view('user.jadwal-belajar.create', compact('savedMaterials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'material_id' => 'required|exists:materials,id',
            'pengulangan' => 'required|in:tidak,harian,mingguan',
        ]);

        // Verify that the material is in user's saved list
        $material = Material::findOrFail($validated['material_id']);
        $isSaved = UserSavedMaterial::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->exists();
            
        if (!$isSaved) {
            return back()->withErrors(['material_id' => 'Anda hanya dapat membuat jadwal dari materi yang tersimpan.']);
        }

        // Generate judul dari material
        $validated['judul'] = $material->judul;
        $validated['materi'] = $material->judul; // Keep compatibility
        $validated['user_id'] = Auth::id();
        
        // Remove material_id as it's not in the table
        unset($validated['material_id']);

        Jadwal::create($validated);

        return redirect()->route('user.jadwal-belajar')
            ->with('success', 'Jadwal berhasil dibuat!');
    }

    public function edit(Jadwal $jadwal)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        // Get saved materials for the current user
        $savedMaterials = $this->getUserSavedMaterials();

        return view('user.jadwal-belajar.edit', compact('jadwal', 'savedMaterials'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'material_id' => 'required|exists:materials,id',
            'pengulangan' => 'required|in:tidak,harian,mingguan',
        ]);

        // Verify that the material is in user's saved list
        $material = Material::findOrFail($validated['material_id']);
        $isSaved = UserSavedMaterial::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->exists();
            
        if (!$isSaved) {
            return back()->withErrors(['material_id' => 'Anda hanya dapat membuat jadwal dari materi yang tersimpan.']);
        }

        // Update judul dari material
        $validated['judul'] = $material->judul;
        $validated['materi'] = $material->judul; // Keep compatibility
        
        // Remove material_id as it's not in the table
        unset($validated['material_id']);

        $jadwal->update($validated);

        return redirect()->route('user.jadwal-belajar')
            ->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Jadwal $jadwal)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        $jadwal->delete();

        return redirect()->route('user.jadwal-belajar')
            ->with('success', 'Jadwal berhasil dihapus!');
    }

    public function startSession(Jadwal $jadwal)
    {
        // Get available devices for this user's lembaga
        $devices = Device::where('status', 'aktif')
            ->where(function($query) {
                $query->where('lembaga_id', Auth::user()->lembaga_id)
                      ->orWhere('user_id', Auth::id());
            })
            ->get();

        return view('user.jadwal-belajar.select-device', compact('jadwal', 'devices'));
    }

    public function sendToDevices(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'devices' => 'required|array',
            'devices.*' => 'exists:devices,id'
        ]);

        $devices = Device::whereIn('id', $validated['devices'])->get();

        // Send material to selected devices via MQTT
        foreach ($devices as $device) {
            try {
                $this->mqttService->sendMaterial($device->serial_number, [
                    'jadwal_id' => $jadwal->id,
                    'judul' => $jadwal->judul,
                    'materi' => $jadwal->materi,
                    'user' => Auth::user()->nama_lengkap,
                    'timestamp' => now()->toISOString()
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send material to device: ' . $e->getMessage());
            }
        }

        // Update jadwal status
        $jadwal->update(['status' => 'sedang_berlangsung']);

        return redirect()->route('user.jadwal-belajar.learn', $jadwal)
            ->with('success', 'Materi berhasil dikirim ke perangkat!');
    }

    public function learn(Jadwal $jadwal)
    {
        // Generate sample braille data (in real app, this would come from database)
        $brailleData = $this->generateSampleBrailleData($jadwal->materi);

        return view('user.jadwal-belajar.learn', compact('jadwal', 'brailleData'));
    }

    /**
     * Get user's saved materials
     */
    private function getUserSavedMaterials()
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        // Get materials that are both saved by user and accessible
        return Material::whereIn('id', function($query) use ($user) {
            $query->select('material_id')
                ->from('user_saved_materials')
                ->where('user_id', $user->id);
        })
        ->where('status', 'published')
        ->where(function($q) use ($userLembagaId) {
            $q->where('akses', 'public')
              ->orWhere('akses', $userLembagaId);
        })
        ->orderBy('judul')
        ->get();
    }

    private function generateSampleBrailleData($materi)
    {
        // Sample data - in production, this would come from material database
        $text = $materi ?? "Pengenalan Braille";
        $data = [];
        $chars = str_split($text);
        
        $brailleMap = [
            'A' => '100000', 'B' => '110000', 'C' => '100100',
            'D' => '100110', 'E' => '100010', 'F' => '110100',
            'G' => '110110', 'H' => '110010', 'I' => '010100',
            'J' => '010110', 'K' => '101000', 'L' => '111000',
            'M' => '101100', 'N' => '101110', 'O' => '101010',
            'P' => '111100', 'Q' => '111110', 'R' => '111010',
            'S' => '011100', 'T' => '011110', 'U' => '101001',
            'V' => '111001', 'W' => '010111', 'X' => '101101',
            'Y' => '101111', 'Z' => '101011', ' ' => '000000',
        ];

        $page = 1;
        $charPerPage = 10;
        
        foreach ($chars as $index => $char) {
            $upperChar = strtoupper($char);
            $braille = $brailleMap[$upperChar] ?? '000000';
            
            if ($index > 0 && $index % $charPerPage == 0) {
                $page++;
            }
            
            $data[] = [
                'karakter' => $char,
                'braille' => $braille,
                'halaman' => $page
            ];
        }

        return json_encode($data);
    }
}