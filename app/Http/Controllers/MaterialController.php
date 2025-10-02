<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\BrailleContent;
use App\Models\MaterialBrailleContent;
use App\Services\PdfConversionService;
use App\Services\PdfToJsonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Queue;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('creator');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $materials = $query->paginate(10)->withQueryString();
        
        return view('admin.manajemen-materi.index', compact('materials'));
    }
    
    public function create()
    {
        return view('admin.manajemen-materi.create');
    }
    
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
            'file' => 'required|file|mimes:pdf|max:40960', // 40MB max
            'akses' => 'required'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Store PDF file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('materials/pdf', $fileName, 'private');
        
        // Create material record
        $material = Material::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tahun_terbit' => $request->tahun_terbit,
            'penerbit' => $request->penerbit,
            'edisi' => $request->edisi,
            'kategori' => $request->kategori ?: null,
            'tingkat' => $request->tingkat,
            'file_path' => $filePath,
            'total_halaman' => 0, // Will be updated after processing
            'status' => 'processing',
            'akses' => $request->akses,
            'created_by' => Auth::id()
        ]);
        
        // Trigger PDF to Braille conversion process
        $this->triggerPdfConversion($material);
        
        return redirect()->route('admin.manajemen-materi')
            ->with('success', 'Materi berhasil diupload! Proses konversi sedang berlangsung.');
    }
    
    public function show(Material $material)
    {
        $material->load('creator');
        return view('admin.manajemen-materi.show', compact('material'));
    }
    
    public function edit(Material $material)
    {
        return view('admin.manajemen-materi.edit', compact('material'));
    }
    
    public function update(Request $request, Material $material)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'penerbit' => 'nullable|string|max:255',
            'edisi' => 'nullable|string|max:100',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'status' => 'required|in:draft,processing,review,published,archived,pending',
            'akses' => 'required',
            'file' => 'nullable|file|mimes:pdf|max:10240' // 10MB max
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Handle file upload if new file is provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path && Storage::disk('private')->exists($material->file_path)) {
                Storage::disk('private')->delete($material->file_path);
            }
            
            // Store new PDF file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materials/pdf', $fileName, 'private');
            
            // Delete old JSON data if exists
            $pdfToJsonService = new PdfToJsonService();
            $pdfToJsonService->deleteMaterialJsonData($material);
        } else {
            $filePath = $material->file_path; // Keep existing file
        }
        
        $material->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tahun_terbit' => $request->tahun_terbit,
            'penerbit' => $request->penerbit,
            'edisi' => $request->edisi,
            'kategori' => $request->kategori ?: null,
            'tingkat' => $request->tingkat,
            'status' => $request->status,
            'akses' => $request->akses,
            'file_path' => $filePath,
            'published_at' => $request->status === 'published' ? now() : null
        ]);
        
        // Trigger PDF to Braille conversion if new file was uploaded
        if ($request->hasFile('file')) {
            $this->triggerPdfConversion($material);
        }
        
        return redirect()->route('admin.manajemen-materi')
            ->with('success', 'Materi berhasil diperbarui!');
    }
    
    public function updateStatus(Request $request, Material $material)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,processing,review,published,archived'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => 'Status tidak valid'], 400);
        }
        
        $material->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $material->status,
            'status_badge_color' => $material->status_badge_color
        ]);
    }

    public function destroy(Material $material)
    {
        // Delete associated files
        if ($material->file_path) {
            Storage::disk('private')->delete($material->file_path);
        }
        if ($material->braille_data_path) {
            Storage::disk('private')->delete($material->braille_data_path);
        }
        
        // Delete JSON data
        $pdfToJsonService = new PdfToJsonService();
        $pdfToJsonService->deleteMaterialJsonData($material);
        
        $material->delete();
        
        return redirect()->route('admin.manajemen-materi')
            ->with('success', 'Materi berhasil dihapus!');
    }
    
    /**
     * Preview material content as JSON
     */
    public function preview(Material $material)
    {
        try {
            // Check if JSON file exists (since we now store JSON instead of PDF)
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return response()->json([
                    'error' => 'File JSON tidak ditemukan untuk materi ini'
                ], 404);
            }

            // Read the saved JSON file directly
            try {
                $jsonContent = Storage::disk('private')->get($material->file_path);
                $jsonData = json_decode($jsonContent, true);
                
                if ($jsonData && isset($jsonData['pages']) && !empty($jsonData['pages'])) {
                    // Return the saved JSON data
                    return response()->json($jsonData);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to read JSON file: ' . $e->getMessage());
            }

            // Fallback: Return basic material info if PDF conversion fails
            $jsonData = [
                'judul' => $material->judul,
                'penerbit' => $material->penerbit,
                'tahun' => $material->tahun_terbit,
                'edisi' => $material->edisi,
                'pages' => [
                    [
                        'page' => 1,
                        'lines' => [
                            [
                                'line' => 1,
                                'text' => 'PDF conversion failed. Material info:'
                            ],
                            [
                                'line' => 2,
                                'text' => 'Judul: ' . $material->judul
                            ],
                            [
                                'line' => 3,
                                'text' => 'Kategori: ' . ($material->kategori ?? 'Tidak ada')
                            ],
                            [
                                'line' => 4,
                                'text' => 'Tingkat: ' . ($material->tingkat ?? 'Tidak ada')
                            ],
                            [
                                'line' => 5,
                                'text' => 'Status: ' . ($material->status ?? 'Tidak ada')
                            ]
                        ]
                    ]
                ]
            ];
            
            return response()->json($jsonData);
            
        } catch (\Exception $e) {
            \Log::error('Preview error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat preview: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Download material as JSON file
     */
    public function downloadJson(Material $material)
    {
        try {
            // Check if JSON file exists
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return redirect()->back()->with('error', 'File JSON tidak ditemukan');
            }
            
            // Read the saved JSON file
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);
            
            if (!$jsonData) {
                return redirect()->back()->with('error', 'Tidak dapat memuat data JSON');
            }
            
            $filename = 'materi_' . $material->id . '_' . time() . '.json';
            
            return response()->json($jsonData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh JSON: ' . $e->getMessage());
        }
    }
    
    // User-facing methods
    public function library(Request $request)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $query = Material::published()->with('creator');
        
        // Access control: Only show materials the user has access to
        $query->where(function($q) use ($userLembagaId) {
            // Public materials - accessible to everyone
            $q->where('akses', 'public');
            
            // Lembaga-specific materials - only accessible to users from that lembaga
            if ($userLembagaId) {
                $q->orWhere('akses', $userLembagaId);
            }
        });
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter by level
        if ($request->has('tingkat') && $request->tingkat) {
            $query->where('tingkat', $request->tingkat);
        }
        
        $materials = $query->paginate(12)->withQueryString();
        
        return view('user.perpustakaan', compact('materials'));
    }
    
    public function requestMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_materi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'prioritas' => 'required|in:rendah,sedang,tinggi'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        MaterialRequest::create([
            'judul_materi' => $request->judul_materi,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'tingkat' => $request->tingkat,
            'prioritas' => $request->prioritas,
            'status' => 'pending',
            'requested_by' => Auth::id()
        ]);
        
        return redirect()->route('user.request-materi')
            ->with('success', 'Request materi berhasil dikirim! Admin akan meninjau request Anda.');
    }
    
    public function myRequests()
    {
        $requests = MaterialRequest::where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.my-requests', compact('requests'));
    }

    /**
     * Trigger PDF to JSON conversion and delete PDF
     */
    private function triggerPdfConversion(Material $material)
    {
        try {
            // Use PdfToJsonService for conversion
            $pdfToJsonService = new PdfToJsonService();
            $pdfPath = Storage::disk('private')->path($material->file_path);
            
            $options = [
                'judul' => $material->judul,
                'penerbit' => $material->penerbit,
                'tahun' => $material->tahun_terbit,
                'edisi' => $material->edisi
            ];
            
            // Convert PDF to JSON
            $jsonData = $pdfToJsonService->convertPdfToJson($pdfPath, $options);
            
            if ($jsonData) {
                // Save JSON data
                $jsonPath = 'materials/json/' . $material->id . '.json';
                Storage::disk('private')->put($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT));
                
                // Store original PDF path before updating
                $originalPdfPath = $material->file_path;
                
                // Update material with JSON path
                $material->update([
                    'file_path' => $jsonPath, // Replace PDF path with JSON path
                    'total_halaman' => count($jsonData['pages'] ?? []),
                    'status' => 'review'
                ]);
                
                // Delete the original PDF file
                if (Storage::disk('private')->exists($originalPdfPath)) {
                    Storage::disk('private')->delete($originalPdfPath);
                }
                
                \Log::info("PDF conversion completed for material {$material->id}. PDF deleted, JSON saved.");
                
                // Now convert JSON to Braille
                $conversionService = new PdfConversionService();
                $brailleResult = $conversionService->convertPdfToBraille($material);
                
                if ($brailleResult) {
                    \Log::info("Braille conversion completed for material {$material->id}");
                    $material->update(['status' => 'review']);
                } else {
                    \Log::warning("Braille conversion failed for material {$material->id}");
                    $material->update(['status' => 'review']); // Still mark as review since JSON is available
                }
            } else {
                throw new \Exception('PDF conversion returned no data');
            }
            
        } catch (\Exception $e) {
            // Log error and update material status
            \Log::error("PDF conversion failed for material {$material->id}: " . $e->getMessage());
            $material->update(['status' => 'draft']);
        }
    }

    /**
     * Show material details with Braille content
     */
    public function showWithBraille(Material $material)
    {
        $material->load(['creator', 'brailleContents']);
        
        $conversionService = new PdfConversionService();
        $conversionStatus = $conversionService->getConversionStatus($material);
        
        return view('admin.manajemen-materi.show-braille', compact('material', 'conversionStatus'));
    }

    /**
     * Download material file
     */
    public function download(Material $material)
    {
        // Check access permissions
        $this->checkMaterialAccess($material);
        
        if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk('private')->download($material->file_path, $material->judul . '.pdf');
    }

    /**
     * Get Braille content for preview
     */
    public function getBrailleContent(Material $material)
    {
        try {
            // Check access permissions
            $this->checkMaterialAccess($material);
            
            // Get braille content from database
            $brailleContents = MaterialBrailleContent::where('material_id', $material->id)
                ->orderBy('page_number')
                ->get();
            
            if ($brailleContents->isEmpty()) {
                return response()->json([
                    'error' => 'Tidak ada konten braille tersedia untuk materi ini'
                ], 404);
            }
            
            // Get metadata from JSON file (not database)
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);
            
            // Build JSON structure with braille-converted metadata from JSON file
            $brailleJson = [
                'judul' => $this->convertToBraille($jsonData['judul'] ?? ''),
                'penerbit' => $this->convertToBraille($jsonData['penerbit'] ?? ''),
                'tahun' => $this->convertToBraille($jsonData['tahun'] ?? ''),
                'edisi' => $this->convertToBraille($jsonData['edisi'] ?? ''),
                'pages' => []
            ];
            
            // Group by page number and build lines
            $pages = [];
            foreach ($brailleContents as $content) {
                $pageNumber = $content->page_number;
                if (!isset($pages[$pageNumber])) {
                    $pages[$pageNumber] = [
                        'page' => $pageNumber,
                        'lines' => []
                    ];
                }
                
                // Split braille text into lines
                $lines = explode("\n", $content->braille_text);
                foreach ($lines as $index => $line) {
                    if (!empty(trim($line))) {
                        $pages[$pageNumber]['lines'][] = [
                            'line' => count($pages[$pageNumber]['lines']) + 1,
                            'text' => $line
                        ];
                    }
                }
            }
            
            // Convert to array and sort by page number
            $brailleJson['pages'] = array_values($pages);
            
            return response()->json($brailleJson);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memuat konten braille: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert text to braille
     */
    private function convertToBraille($text)
    {
        if (empty($text)) {
            return '';
        }

        $brailleMap = [
            'a' => '⠁', 'b' => '⠃', 'c' => '⠉', 'd' => '⠙', 'e' => '⠑',
            'f' => '⠋', 'g' => '⠛', 'h' => '⠓', 'i' => '⠊', 'j' => '⠚',
            'k' => '⠅', 'l' => '⠇', 'm' => '⠍', 'n' => '⠝', 'o' => '⠕',
            'p' => '⠏', 'q' => '⠟', 'r' => '⠗', 's' => '⠎', 't' => '⠞',
            'u' => '⠥', 'v' => '⠧', 'w' => '⠺', 'x' => '⠭', 'y' => '⠽', 'z' => '⠵',
            ' ' => '⠀', // Braille space
            '1' => '⠁', '2' => '⠃', '3' => '⠉', '4' => '⠙', '5' => '⠑',
            '6' => '⠋', '7' => '⠛', '8' => '⠓', '9' => '⠊', '0' => '⠚',
            ',' => '⠂', // Comma
            '.' => '⠲', // Period
            ':' => '⠂⠂', // Colon
            ';' => '⠂⠂', // Semicolon
            '!' => '⠂⠂', // Exclamation
            '?' => '⠂⠂', // Question mark
            '-' => '⠤', // Hyphen
            '(' => '⠐⠣', // Opening parenthesis
            ')' => '⠐⠜', // Closing parenthesis
            '[' => '⠐⠣', // Opening bracket
            ']' => '⠐⠜', // Closing bracket
            '"' => '⠐⠦', // Opening quote
            '"' => '⠐⠴', // Closing quote
            "'" => '⠐⠦', // Apostrophe
            "'" => '⠐⠴', // Closing apostrophe
        ];

        $result = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = strtolower($text[$i]);
            $result .= $brailleMap[$char] ?? '⠿'; // Unknown character symbol
        }

        return $result;
    }

    /**
     * Download Braille content
     */
    public function downloadBraille(Material $material)
    {
        // Check access permissions
        $this->checkMaterialAccess($material);
        
        if (!$material->braille_data_path || !Storage::disk('private')->exists($material->braille_data_path)) {
            abort(404, 'Braille content not found');
        }
        
        $fileName = 'Braille_' . $material->judul . '_' . time() . '.json';
        return Storage::disk('private')->download($material->braille_data_path, $fileName);
    }

    /**
     * Check if user has access to material
     */
    private function checkMaterialAccess(Material $material)
    {
        $user = Auth::user();
        
        // Admin can access everything
        if ($user->isAdmin()) {
            return true;
        }
        
        // Check access level
        switch ($material->akses) {
            case 'public':
                return true;
                
            case 'premium':
                // Add premium user check here if you implement premium features
                return true; // For now, allow all users
                
            case 'restricted':
                // Only users from the same institution
                return $user->lembaga_id === $material->creator->lembaga_id;
                
            default:
                return false;
        }
    }

    /**
     * Reconvert material (admin only)
     */
    public function reconvert(Material $material)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        
        $conversionService = new PdfConversionService();
        $success = $conversionService->reconvert($material);
        
        if ($success) {
            return redirect()->back()->with('success', 'Materi berhasil dikonversi ulang!');
        } else {
            return redirect()->back()->with('error', 'Gagal mengkonversi ulang materi. Periksa log untuk detail.');
        }
    }

    /**
     * Test PDF conversion (for debugging)
     */
    public function testConversion()
    {
        try {
            $pdfToJsonService = new PdfToJsonService();
            
            // Test with a simple command
            $testCommand = 'C:\Python313\python.exe --version';
            $process = new Process(['cmd', '/c', $testCommand]);
            $process->run();
            $result = $process;
            
            return response()->json([
                'python_version' => $result->getOutput(),
                'python_error' => $result->getErrorOutput(),
                'success' => $result->isSuccessful()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Preview PDF conversion to JSON (for upload page)
     */
    public function previewConversion(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'judul' => 'nullable|string|max:255',
                'penerbit' => 'nullable|string|max:255',
                'tahun' => 'nullable|integer|min:1900|max:' . date('Y'),
                'edisi' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid file or parameters'], 400);
            }

            $file = $request->file('file');
            $tempPath = $file->store('temp', 'private');
            
            try {
                $pdfToJsonService = new PdfToJsonService();
                $convertedData = $pdfToJsonService->convertPdfToJson(
                    Storage::disk('private')->path($tempPath),
                    $request->judul,
                    $request->penerbit,
                    $request->tahun,
                    $request->edisi
                );
                
                // Clean up temp file
                Storage::disk('private')->delete($tempPath);
                
                if ($convertedData && isset($convertedData['pages']) && !empty($convertedData['pages'])) {
                    return response()->json($convertedData);
                } else {
                    return response()->json(['error' => 'Failed to convert PDF to JSON'], 500);
                }
                
            } catch (\Exception $e) {
                // Clean up temp file on error
                Storage::disk('private')->delete($tempPath);
                \Log::error('PDF conversion failed in preview: ' . $e->getMessage());
                return response()->json(['error' => 'PDF conversion failed: ' . $e->getMessage()], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Preview conversion error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengkonversi PDF: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * User preview - returns HTML view instead of JSON
     */
    public function userPreview(Material $material)
    {
        try {
            // Check if JSON file exists
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return view('user.material-preview', [
                    'material' => $material,
                    'error' => 'File JSON tidak ditemukan untuk materi ini'
                ]);
            }

            // Read the saved JSON file directly
            try {
                $jsonContent = Storage::disk('private')->get($material->file_path);
                $jsonData = json_decode($jsonContent, true);
                
                if ($jsonData && isset($jsonData['pages']) && !empty($jsonData['pages'])) {
                    return view('user.material-preview', [
                        'material' => $material,
                        'jsonData' => $jsonData
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to read JSON file: ' . $e->getMessage());
            }

            // Fallback: Return basic material info if PDF conversion fails
            $jsonData = [
                'judul' => $material->judul,
                'penerbit' => $material->penerbit,
                'tahun' => $material->tahun_terbit,
                'edisi' => $material->edisi,
                'pages' => [
                    [
                        'page' => 1,
                        'lines' => [
                            [
                                'line' => 1,
                                'text' => 'PDF conversion failed. Material info:'
                            ],
                            [
                                'line' => 2,
                                'text' => 'Judul: ' . $material->judul
                            ],
                            [
                                'line' => 3,
                                'text' => 'Kategori: ' . ($material->kategori ?? 'Tidak ada')
                            ],
                            [
                                'line' => 4,
                                'text' => 'Tingkat: ' . ($material->tingkat ?? 'Tidak ada')
                            ],
                            [
                                'line' => 5,
                                'text' => 'Status: ' . ($material->status ?? 'Tidak ada')
                            ]
                        ]
                    ]
                ]
            ];
            
            return view('user.material-preview', [
                'material' => $material,
                'jsonData' => $jsonData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('User preview error: ' . $e->getMessage());
            return view('user.material-preview', [
                'material' => $material,
                'error' => 'Gagal memuat preview: ' . $e->getMessage()
            ]);
        }
    }
}