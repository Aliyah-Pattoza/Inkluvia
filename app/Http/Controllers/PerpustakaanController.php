<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Device;
use App\Models\UserSavedMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PerpustakaanController extends Controller
{
    /**
     * Display listing of materials available to user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $query = Material::query()
            ->where('status', 'published')
            ->with('creator');

        // Filter berdasarkan hak akses - sesuai dengan aturan admin
        $query->where(function($q) use ($userLembagaId) {
            // Materi publik - dapat diakses semua orang
            $q->where('akses', 'public');
            
            // Materi khusus lembaga - hanya dapat diakses user dari lembaga tersebut
            if ($userLembagaId) {
                $q->orWhere('akses', $userLembagaId);
            }
        });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // Sorting
        $sortBy = $request->get('sort', 'judul');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortBy, ['judul', 'tahun_terbit', 'published_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $materials = $query->paginate(12)->withQueryString();

        // Get user's saved materials
        $userSavedMaterials = UserSavedMaterial::where('user_id', Auth::id())
            ->pluck('material_id')
            ->toArray();

        // Get user's devices
        $userDevices = Device::where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->get();

        return view('user.perpustakaan', compact('materials', 'userSavedMaterials', 'userDevices'));
    }

    /**
     * Show saved materials page
     */
    public function savedMaterials(Request $request)
    {
        $user = Auth::user();
        
        $query = Material::query()
            ->whereIn('id', function($subQuery) use ($user) {
                $subQuery->select('material_id')
                    ->from('user_saved_materials')
                    ->where('user_id', $user->id);
            })
            ->where('status', 'published')
            ->with('creator');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $materials = $query->paginate(12)->withQueryString();

        // Get user's saved materials
        $userSavedMaterials = UserSavedMaterial::where('user_id', Auth::id())
            ->pluck('material_id')
            ->toArray();

        return view('user.materi-tersimpan', compact('materials', 'userSavedMaterials'));
    }

    /**
     * Toggle saved status
     */
    public function toggleSaved(Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke materi ini'
            ], 403);
        }

        $userId = Auth::id();
        $saved = UserSavedMaterial::where('user_id', $userId)
            ->where('material_id', $material->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
            $message = 'Materi berhasil dihapus dari daftar tersimpan';
        } else {
            UserSavedMaterial::create([
                'user_id' => $userId,
                'material_id' => $material->id,
                'saved_at' => now()
            ]);
            $isSaved = true;
            $message = 'Materi berhasil disimpan';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_saved' => $isSaved,
                'message' => $message
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Preview material content for screen readers
     */
    public function preview(Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke materi ini'
            ], 403);
        }

        try {
            // Get JSON content
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konten materi tidak tersedia'
                ], 404);
            }

            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);

            // Format content for screen reader
            $previewData = [
                'id' => $material->id,
                'judul' => $material->judul,
                'penerbit' => $material->penerbit ?? 'Tidak ada',
                'tahun' => $material->tahun_terbit ?? 'Tidak ada',
                'kategori' => \App\Models\Material::getKategoriOptions()[$material->kategori] ?? $material->kategori,
                'tingkat' => \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? $material->tingkat,
                'total_halaman' => count($jsonData['pages'] ?? []),
                'deskripsi' => $material->deskripsi ?? '',
                'preview_text' => $this->extractPreviewText($jsonData),
                'full_content' => $this->formatFullContent($jsonData)
            ];

            return response()->json([
                'success' => true,
                'data' => $previewData
            ]);

        } catch (\Exception $e) {
            Log::error('Preview error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat preview materi'
            ], 500);
        }
    }

    /**
     * Show detailed preview page
     */
    public function showPreview(Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        try {
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);
            
            $isSaved = UserSavedMaterial::where('user_id', Auth::id())
                ->where('material_id', $material->id)
                ->exists();

            return view('user.preview-materi', compact('material', 'jsonData', 'isSaved'));
            
        } catch (\Exception $e) {
            Log::error('Preview page error: ' . $e->getMessage());
            return view('user.preview-materi', [
                'material' => $material,
                'error' => 'Gagal memuat konten materi'
            ]);
        }
    }

    /**
     * Send material to EduBraille device
     */
    public function sendToDevice(Request $request, Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            return view('user.kirim-braille', [
                'error' => 'Anda tidak memiliki akses ke materi ini'
            ]);
        }

        // Get user's devices
        $device = null;
        if ($request->filled('device_id')) {
            $device = Device::where('id', $request->device_id)
                ->where('user_id', Auth::id())
                ->where('status', 'aktif')
                ->first();
        }

        // Get braille content
        try {
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);
            
            // Convert to braille format
            $brailleJson = $this->convertToBrailleFormat($jsonData);
            
            return view('user.kirim-braille', compact('material', 'device', 'brailleJson'));
            
        } catch (\Exception $e) {
            Log::error('Failed to load braille content: ' . $e->getMessage());
            return view('user.kirim-braille', [
                'error' => 'Gagal memuat konten braille'
            ]);
        }
    }

    /**
     * Extract preview text from JSON data
     */
    private function extractPreviewText($jsonData, $maxLines = 10)
    {
        $previewText = [];
        $lineCount = 0;

        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            foreach ($jsonData['pages'] as $page) {
                if ($lineCount >= $maxLines) break;
                
                if (isset($page['lines']) && is_array($page['lines'])) {
                    foreach ($page['lines'] as $line) {
                        if ($lineCount >= $maxLines) break;
                        if (isset($line['text']) && !empty(trim($line['text']))) {
                            $previewText[] = trim($line['text']);
                            $lineCount++;
                        }
                    }
                }
            }
        }

        return implode(' ', $previewText);
    }

    /**
     * Format full content for detailed view
     */
    private function formatFullContent($jsonData)
    {
        $content = [];

        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            foreach ($jsonData['pages'] as $page) {
                $pageContent = [
                    'page_number' => $page['page'] ?? 1,
                    'lines' => []
                ];
                
                if (isset($page['lines']) && is_array($page['lines'])) {
                    foreach ($page['lines'] as $line) {
                        if (isset($line['text']) && !empty(trim($line['text']))) {
                            $pageContent['lines'][] = trim($line['text']);
                        }
                    }
                }
                
                if (!empty($pageContent['lines'])) {
                    $content[] = $pageContent;
                }
            }
        }

        return $content;
    }

    /**
     * Convert JSON to braille format for EduBraille
     */
    private function convertToBrailleFormat($jsonData)
    {
        $brailleData = [];
        $halaman = 1;
        $counter = 1;

        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            foreach ($jsonData['pages'] as $pageIndex => $page) {
                if (isset($page['lines']) && is_array($page['lines'])) {
                    foreach ($page['lines'] as $line) {
                        if (isset($line['text'])) {
                            // Convert each character to braille
                            $text = $line['text'];
                            for ($i = 0; $i < strlen($text); $i++) {
                                $char = $text[$i];
                                $braillePattern = $this->charToBraillePattern($char);
                                
                                $brailleData[] = [
                                    'halaman' => $halaman,
                                    'karakter' => $char,
                                    'braille' => $braillePattern
                                ];
                                
                                $counter++;
                                // 10 karakter per halaman untuk demo
                                if ($counter % 10 == 0) {
                                    $halaman++;
                                }
                            }
                        }
                    }
                }
            }
        }

        return json_encode($brailleData);
    }

    /**
     * Convert character to 6-bit braille pattern
     */
    private function charToBraillePattern($char)
    {
        // Simplified braille mapping (6-bit pattern)
        $brailleMap = [
            'a' => '100000', 'b' => '110000', 'c' => '100100',
            'd' => '100110', 'e' => '100010', 'f' => '110100',
            'g' => '110110', 'h' => '110010', 'i' => '010100',
            'j' => '010110', 'k' => '101000', 'l' => '111000',
            'm' => '101100', 'n' => '101110', 'o' => '101010',
            'p' => '111100', 'q' => '111110', 'r' => '111010',
            's' => '011100', 't' => '011110', 'u' => '101001',
            'v' => '111001', 'w' => '010111', 'x' => '101101',
            'y' => '101111', 'z' => '101011', ' ' => '000000',
            '1' => '100000', '2' => '110000', '3' => '100100',
            '4' => '100110', '5' => '100010', '6' => '110100',
            '7' => '110110', '8' => '110010', '9' => '010100',
            '0' => '010110'
        ];

        $char = strtolower($char);
        return $brailleMap[$char] ?? '111111'; // Default pattern for unknown chars
    }
}