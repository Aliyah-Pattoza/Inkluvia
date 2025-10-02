<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\UserSavedMaterial;
use App\Models\MaterialBrailleContent;
use App\Services\PdfToJsonService;
use App\Services\OpenAIBrailleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserMaterialController extends Controller
{
    protected $pdfToJsonService;
    protected $openAIService;

    public function __construct()
    {
        $this->pdfToJsonService = new PdfToJsonService();
        $this->openAIService = new OpenAIBrailleService();
    }

    /**
     * Display user's materials (both uploaded and saved)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's uploaded materials and saved materials
        $query = Material::query()
            ->where(function($q) use ($user) {
                // Materials created by user
                $q->where('created_by', $user->id)
                  // OR materials saved by user
                  ->orWhereIn('id', function($subQuery) use ($user) {
                      $subQuery->select('material_id')
                          ->from('user_saved_materials')
                          ->where('user_id', $user->id);
                  });
            })
            ->with('creator');

        // Search
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

        // Filter status (only for user's own materials)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort', 'updated_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $materials = $query->paginate(12)->withQueryString();

        // Get user's saved material IDs
        $userSavedMaterials = UserSavedMaterial::where('user_id', $user->id)
            ->pluck('material_id')
            ->toArray();

        return view('user.materi-saya.index', compact('materials', 'userSavedMaterials'));
    }

    /**
     * Show create material form
     */
    public function create()
    {
        return view('user.materi-saya.create');
    }

    /**
     * Store new material with AI Braille conversion
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'penerbit' => 'nullable|string|max:255',
            'edisi' => 'nullable|string|max:100',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:40960', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Store PDF file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materials/pdf', $fileName, 'private');

            // Create material record (status: processing)
            $material = Material::create([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'tahun_terbit' => $request->tahun_terbit,
                'penerbit' => $request->penerbit,
                'edisi' => $request->edisi,
                'kategori' => $request->kategori ?: null,
                'tingkat' => $request->tingkat,
                'file_path' => $filePath,
                'total_halaman' => 0,
                'status' => 'processing',
                'akses' => 'public', // User materials are public by default
                'created_by' => Auth::id(),
                'user_id' => Auth::id(),
            ]);

            // Trigger conversion process
            $this->processConversion($material);

            return redirect()->route('user.materi-saya')
                ->with('success', 'Materi berhasil diupload! Proses konversi Braille sedang berlangsung.');

        } catch (\Exception $e) {
            Log::error('Material upload failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengupload materi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit(Material $material)
    {
        // Check ownership
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit materi ini');
        }

        return view('user.materi-saya.edit', compact('material'));
    }

    /**
     * Update material
     */
    public function update(Request $request, Material $material)
    {
        // Check ownership
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit materi ini');
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'penerbit' => 'nullable|string|max:255',
            'edisi' => 'nullable|string|max:100',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:40960',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'tahun_terbit' => $request->tahun_terbit,
                'penerbit' => $request->penerbit,
                'edisi' => $request->edisi,
                'kategori' => $request->kategori ?: null,
                'tingkat' => $request->tingkat,
            ];

            // Handle new file upload
            if ($request->hasFile('file')) {
                // Delete old files
                if ($material->file_path) {
                    Storage::disk('private')->delete($material->file_path);
                }
                
                // Delete old JSON and Braille data
                $this->pdfToJsonService->deleteMaterialJsonData($material);
                MaterialBrailleContent::where('material_id', $material->id)->delete();
                
                if ($material->braille_data_path) {
                    Storage::disk('private')->delete($material->braille_data_path);
                }

                // Store new file
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('materials/pdf', $fileName, 'private');
                
                $updateData['file_path'] = $filePath;
                $updateData['status'] = 'processing';
                $updateData['total_halaman'] = 0;
                $updateData['braille_data_path'] = null;
            }

            $material->update($updateData);

            // Reconvert if new file uploaded
            if ($request->hasFile('file')) {
                $this->processConversion($material);
            }

            return redirect()->route('user.materi-saya')
                ->with('success', 'Materi berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Material update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui materi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete material
     */
    public function destroy(Material $material)
    {
        // Check ownership
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus materi ini');
        }

        try {
            // Delete files
            if ($material->file_path) {
                Storage::disk('private')->delete($material->file_path);
            }
            
            if ($material->braille_data_path) {
                Storage::disk('private')->delete($material->braille_data_path);
            }

            // Delete JSON data
            $this->pdfToJsonService->deleteMaterialJsonData($material);
            
            // Delete Braille content
            MaterialBrailleContent::where('material_id', $material->id)->delete();
            
            // Delete saved references
            UserSavedMaterial::where('material_id', $material->id)->delete();

            $material->delete();

            return redirect()->route('user.materi-saya')
                ->with('success', 'Materi berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Material deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }

    /**
     * Preview material (text and braille)
     */
    public function preview(Material $material)
    {
        // Check access
        if ($material->created_by !== Auth::id() && 
            !UserSavedMaterial::where('user_id', Auth::id())
                ->where('material_id', $material->id)
                ->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        try {
            // Get text version (JSON)
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);

            // Get Braille version
            $brailleData = null;
            if ($material->braille_data_path && Storage::disk('private')->exists($material->braille_data_path)) {
                $brailleContent = Storage::disk('private')->get($material->braille_data_path);
                $brailleData = json_decode($brailleContent, true);
            }

            // Check if material is saved
            $isSaved = UserSavedMaterial::where('user_id', Auth::id())
                ->where('material_id', $material->id)
                ->exists();

            return view('user.materi-saya.preview', compact('material', 'jsonData', 'brailleData', 'isSaved'));

        } catch (\Exception $e) {
            Log::error('Preview error: ' . $e->getMessage());
            return view('user.materi-saya.preview', [
                'material' => $material,
                'error' => 'Gagal memuat preview materi'
            ]);
        }
    }

    /**
     * Download material JSON
     */
    public function download(Material $material)
    {
        // Check access
        if ($material->created_by !== Auth::id() && 
            !UserSavedMaterial::where('user_id', Auth::id())
                ->where('material_id', $material->id)
                ->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        $jsonContent = Storage::disk('private')->get($material->file_path);
        $filename = 'materi_' . $material->id . '_' . time() . '.json';

        return response($jsonContent)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Process PDF to JSON and Braille conversion with AI
     */
    private function processConversion(Material $material)
    {
        try {
            $pdfPath = Storage::disk('private')->path($material->file_path);

            // Step 1: Convert PDF to JSON
            $options = [
                'judul' => $material->judul,
                'penerbit' => $material->penerbit,
                'tahun' => $material->tahun_terbit,
                'edisi' => $material->edisi
            ];

            $jsonData = $this->pdfToJsonService->convertPdfToJson($pdfPath, $options);

            if (!$jsonData) {
                throw new \Exception('PDF to JSON conversion failed');
            }

            // Save JSON data
            $jsonPath = 'materials/json/' . $material->id . '.json';
            Storage::disk('private')->put($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT));

            // Delete original PDF
            $originalPdfPath = $material->file_path;
            if (Storage::disk('private')->exists($originalPdfPath)) {
                Storage::disk('private')->delete($originalPdfPath);
            }

            // Update material with JSON path
            $material->update([
                'file_path' => $jsonPath,
                'total_halaman' => count($jsonData['pages'] ?? []),
                'status' => 'processing'
            ]);

            // Step 2: Convert to Braille using OpenAI
            Log::info("Starting AI Braille conversion for material {$material->id}");
            
            $brailleData = $this->openAIService->convertJsonToBraille($jsonData);

            // Save Braille data to database
            $this->saveBrailleToDatabase($material, $brailleData);

            // Save Braille data to file
            $braillePath = 'materials/braille/' . $material->id . '_braille.json';
            Storage::disk('private')->put($braillePath, json_encode($brailleData, JSON_PRETTY_PRINT));

            // Update material status
            $material->update([
                'braille_data_path' => $braillePath,
                'status' => 'published'
            ]);

            Log::info("AI Braille conversion completed for material {$material->id}");

        } catch (\Exception $e) {
            Log::error("Conversion failed for material {$material->id}: " . $e->getMessage());
            $material->update(['status' => 'draft']);
        }
    }

    /**
     * Save Braille data to database
     */
    private function saveBrailleToDatabase(Material $material, $brailleData)
    {
        if (isset($brailleData['pages']) && is_array($brailleData['pages'])) {
            foreach ($brailleData['pages'] as $page) {
                $brailleText = '';
                $originalText = '';
                $lineCount = 0;
                $characterCount = 0;

                if (isset($page['lines']) && is_array($page['lines'])) {
                    foreach ($page['lines'] as $line) {
                        $brailleText .= ($line['text'] ?? '') . "\n";
                        $originalText .= ($line['original'] ?? '') . "\n";
                        $characterCount += mb_strlen($line['text'] ?? '');
                    }
                    $lineCount = count($page['lines']);
                }

                MaterialBrailleContent::create([
                    'material_id' => $material->id,
                    'page_number' => $page['page'] ?? 1,
                    'braille_text' => trim($brailleText),
                    'original_text' => trim($originalText),
                    'metadata' => [
                        'judul' => $brailleData['judul'] ?? '',
                        'penerbit' => $brailleData['penerbit'] ?? '',
                        'tahun' => $brailleData['tahun'] ?? '',
                        'edisi' => $brailleData['edisi'] ?? ''
                    ],
                    'line_count' => $lineCount,
                    'character_count' => $characterCount
                ]);
            }
        }
    }
}