<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfToJsonService
{
    protected $pythonScriptPath;
    protected $tempDir;

    public function __construct()
    {
        $this->pythonScriptPath = base_path('app/Services/pdf_to_json.py');
        $this->tempDir = storage_path('app/temp/pdf_conversion');
        
        // Ensure temp directory exists
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Convert PDF to JSON using Python script
     */
    public function convertPdfToJson($pdfPath, $options = [])
    {
        try {
            // Ensure Python script exists
            if (!file_exists($this->pythonScriptPath)) {
                Log::warning('Python conversion script not found at: ' . $this->pythonScriptPath);
                return null;
            }

            // Ensure temp directory exists
            if (!is_dir($this->tempDir)) {
                mkdir($this->tempDir, 0755, true);
            }

            // Generate output filename based on file hash (for caching)
            $fileHash = md5_file($pdfPath);
            $outputFile = $this->tempDir . '/pdf_json_' . $fileHash . '.json';
            
            // Check if we already have a cached version
            if (file_exists($outputFile) && (time() - filemtime($outputFile)) < 3600) { // 1 hour cache
                Log::info('Using cached JSON conversion for file: ' . basename($pdfPath));
                $cachedData = json_decode(file_get_contents($outputFile), true);
                if ($cachedData) {
                    return $cachedData;
                }
            }
            
            // Check if PDF file exists and is readable
            if (!file_exists($pdfPath) || !is_readable($pdfPath)) {
                Log::error('PDF file not found or not readable: ' . $pdfPath);
                return null;
            }
            
            // Use cmd.exe to run Python with proper environment
            $pythonScript = escapeshellarg($this->pythonScriptPath);
            $pdfFile = escapeshellarg($pdfPath);
            $outputFileEscaped = escapeshellarg($outputFile);
            
            // Build the command with parameters
            $params = [];
            if (isset($options['judul']) && $options['judul']) {
                $params[] = '--judul ' . escapeshellarg($options['judul']);
            }
            if (isset($options['penerbit']) && $options['penerbit']) {
                $params[] = '--penerbit ' . escapeshellarg($options['penerbit']);
            }
            if (isset($options['tahun']) && $options['tahun']) {
                $params[] = '--tahun ' . escapeshellarg($options['tahun']);
            }
            if (isset($options['edisi']) && $options['edisi']) {
                $params[] = '--edisi ' . escapeshellarg($options['edisi']);
            }
            
            $paramString = implode(' ', $params);
            // Use the full path to Python directly (faster)
            $pythonCmd = 'C:\Python313\python.exe';
            
            // Verify Python exists
            if (!file_exists($pythonCmd)) {
                Log::error('Python not found at: ' . $pythonCmd);
                return null;
            }
            
            $cmd = "{$pythonCmd} {$pythonScript} {$pdfFile} -o {$outputFileEscaped} {$paramString}";
            
            Log::info('Executing command via cmd: ' . $cmd);
            
            // Use cmd.exe to run the command
            $command = ['cmd', '/c', $cmd];

            Log::info('Executing PDF conversion command: ' . implode(' ', $command));
            Log::info('PDF file exists: ' . (file_exists($pdfPath) ? 'Yes' : 'No'));
            Log::info('PDF file size: ' . (file_exists($pdfPath) ? filesize($pdfPath) . ' bytes' : 'N/A'));

            // Execute Python script using exec (simpler and more reliable)
            Log::info('Executing Python conversion using exec...');
            $output = [];
            $returnCode = 0;
            exec($cmd, $output, $returnCode);
            
            if ($returnCode !== 0) {
                Log::error('PDF to JSON conversion failed', [
                    'command' => $cmd,
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
                return null;
            }
            
            Log::info('PDF conversion successful');

            // Read the generated JSON file
            if (!file_exists($outputFile)) {
                Log::error('JSON output file not created: ' . $outputFile);
                return null;
            }

            $jsonContent = file_get_contents($outputFile);
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON generated: ' . json_last_error_msg());
                return null;
            }

            // Validate the data structure
            if (!isset($data['pages']) || !is_array($data['pages']) || empty($data['pages'])) {
                Log::warning('PDF conversion resulted in empty or invalid pages data');
                return null;
            }

            // Clean up temporary file
            if (file_exists($outputFile)) {
                unlink($outputFile);
            }

            Log::info('PDF conversion successful', [
                'pages_count' => count($data['pages']),
                'total_lines' => array_sum(array_map(function($page) {
                    return count($page['lines'] ?? []);
                }, $data['pages']))
            ]);

            return $data;

        } catch (\Exception $e) {
            Log::error('PDF to JSON conversion error', [
                'pdf_path' => $pdfPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get JSON data for material preview
     */
    public function getMaterialJsonData($material)
    {
        try {
            // Check if JSON data already exists
            $jsonPath = $this->getJsonPath($material);
            
            if (Storage::disk('private')->exists($jsonPath)) {
                $jsonContent = Storage::disk('private')->get($jsonPath);
                $data = json_decode($jsonContent, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
            }

            // Convert PDF to JSON if not exists
            if ($material->file_path && Storage::disk('private')->exists($material->file_path)) {
                $pdfPath = Storage::disk('private')->path($material->file_path);
                
                $options = [
                    'judul' => $material->judul,
                    'penerbit' => $material->penerbit,
                    'tahun' => $material->tahun_terbit,
                    'edisi' => $material->edisi
                ];

                $jsonData = $this->convertPdfToJson($pdfPath, $options);
                
                if ($jsonData) {
                    // Store JSON data
                    Storage::disk('private')->put($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT));
                    return $jsonData;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get material JSON data', [
                'material_id' => $material->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get JSON file path for material
     */
    protected function getJsonPath($material)
    {
        return 'materials/json/' . $material->id . '.json';
    }

    /**
     * Delete JSON data for material
     */
    public function deleteMaterialJsonData($material)
    {
        $jsonPath = $this->getJsonPath($material);
        if (Storage::disk('private')->exists($jsonPath)) {
            Storage::disk('private')->delete($jsonPath);
        }
    }
}