<?php

namespace App\Services;

use App\Models\Material;
use App\Models\BrailleContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class PdfConversionService
{
    private $pythonScriptPath;
    private $tempDir;

    public function __construct()
    {
        $this->pythonScriptPath = app_path('Services/pdf_converter.py');
        $this->tempDir = storage_path('app/temp');
        
        // Ensure temp directory exists
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Convert PDF to Braille content
     */
    public function convertPdfToBraille(Material $material)
    {
        try {
            Log::info("Starting PDF to Braille conversion for material ID: {$material->id}");

            // Step 1: Extract text from PDF using your Python script
            $jsonData = $this->extractTextFromPdf($material->file_path);
            
            if (!$jsonData) {
                throw new Exception('Failed to extract text from PDF');
            }

            // Step 2: Convert text to Braille
            $brailleContent = $this->convertTextToBraille($jsonData);

            // Step 3: Save Braille content
            $brailleDataPath = $this->saveBrailleContent($material, $brailleContent);

            // Step 4: Update material record
            $material->update([
                'braille_data_path' => $brailleDataPath,
                'total_halaman' => count($brailleContent),
                'status' => 'review' // Ready for review
            ]);

            Log::info("PDF to Braille conversion completed for material ID: {$material->id}");
            
            return true;

        } catch (Exception $e) {
            Log::error("PDF to Braille conversion failed for material ID: {$material->id}. Error: " . $e->getMessage());
            
            // Update material status to indicate failure
            $material->update(['status' => 'draft']);
            
            return false;
        }
    }

    /**
     * Extract text from PDF using your Python script
     */
    private function extractTextFromPdf($filePath)
    {
        $fullPath = Storage::disk('private')->path($filePath);
        
        // Check if Python script exists
        if (!file_exists($this->pythonScriptPath)) {
            throw new Exception('PDF converter script not found. Please add your Python script to: ' . $this->pythonScriptPath);
        }

        // Execute Python script
        $command = "python3 " . escapeshellarg($this->pythonScriptPath) . " " . escapeshellarg($fullPath) . " 2>&1";
        
        Log::info("Executing command: " . $command);
        
        $output = shell_exec($command);
        
        if (!$output) {
            throw new Exception('Python script execution failed');
        }

        // Parse JSON output
        $jsonData = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON output from Python script: ' . json_last_error_msg());
        }

        return $jsonData;
    }

    /**
     * Convert extracted text to Braille
     */
    private function convertTextToBraille($jsonData)
    {
        $brailleContent = [];
        
        // This is a placeholder for Braille conversion
        // You'll need to implement actual Braille conversion logic here
        // or integrate with a Braille conversion service
        
        foreach ($jsonData as $pageIndex => $pageData) {
            $pageContent = $this->convertPageToBraille($pageData);
            $brailleContent[] = [
                'page_number' => (int)$pageIndex + 1,
                'content' => $pageContent,
                'original_text' => $pageData['text'] ?? '',
                'metadata' => $pageData['metadata'] ?? []
            ];
        }

        return $brailleContent;
    }

    /**
     * Convert a single page to Braille
     */
    private function convertPageToBraille($pageData)
    {
        // Placeholder Braille conversion
        // Replace this with actual Braille conversion logic
        
        $text = $pageData['text'] ?? '';
        
        // Simple placeholder conversion (this is NOT real Braille)
        // You should replace this with proper Braille conversion
        $brailleText = $this->placeholderBrailleConversion($text);
        
        return [
            'braille_text' => $brailleText,
            'line_count' => substr_count($brailleText, "\n") + 1,
            'character_count' => strlen($brailleText),
            'conversion_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Placeholder Braille conversion (NOT real Braille)
     * Replace this with actual Braille conversion logic
     */
    private function placeholderBrailleConversion($text)
    {
        // This is just a placeholder - NOT actual Braille conversion
        // You need to implement real Braille conversion here
        
        $brailleMap = [
            'a' => '⠁', 'b' => '⠃', 'c' => '⠉', 'd' => '⠙', 'e' => '⠑',
            'f' => '⠋', 'g' => '⠛', 'h' => '⠓', 'i' => '⠊', 'j' => '⠚',
            'k' => '⠅', 'l' => '⠇', 'm' => '⠍', 'n' => '⠝', 'o' => '⠕',
            'p' => '⠏', 'q' => '⠟', 'r' => '⠗', 's' => '⠎', 't' => '⠞',
            'u' => '⠥', 'v' => '⠧', 'w' => '⠺', 'x' => '⠭', 'y' => '⠽', 'z' => '⠵',
            ' ' => '⠀', // Braille space
            '1' => '⠂', '2' => '⠆', '3' => '⠒', '4' => '⠲', '5' => '⠢',
            '6' => '⠖', '7' => '⠶', '8' => '⠦', '9' => '⠔', '0' => '⠴'
        ];

        $result = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = strtolower($text[$i]);
            $result .= $brailleMap[$char] ?? '⠿'; // Unknown character symbol
        }

        return $result;
    }

    /**
     * Save Braille content to database and file
     */
    private function saveBrailleContent(Material $material, $brailleContent)
    {
        // Save to database
        foreach ($brailleContent as $pageData) {
            BrailleContent::create([
                'material_id' => $material->id,
                'page_number' => $pageData['page_number'],
                'braille_text' => $pageData['content']['braille_text'],
                'original_text' => $pageData['original_text'],
                'metadata' => json_encode($pageData['metadata']),
                'line_count' => $pageData['content']['line_count'],
                'character_count' => $pageData['content']['character_count']
            ]);
        }

        // Save to file
        $fileName = 'braille_' . $material->id . '_' . time() . '.json';
        $filePath = 'materials/braille/' . $fileName;
        
        Storage::disk('private')->put($filePath, json_encode($brailleContent, JSON_PRETTY_PRINT));

        return $filePath;
    }

    /**
     * Get conversion status
     */
    public function getConversionStatus(Material $material)
    {
        $brailleContents = BrailleContent::where('material_id', $material->id)->get();
        
        return [
            'total_pages' => $brailleContents->count(),
            'conversion_complete' => $material->braille_data_path !== null,
            'status' => $material->status,
            'last_updated' => $material->updated_at
        ];
    }

    /**
     * Reconvert material (useful for testing)
     */
    public function reconvert(Material $material)
    {
        // Delete existing Braille content
        BrailleContent::where('material_id', $material->id)->delete();
        
        if ($material->braille_data_path) {
            Storage::disk('private')->delete($material->braille_data_path);
        }

        // Reset material status
        $material->update([
            'braille_data_path' => null,
            'total_halaman' => 0,
            'status' => 'draft'
        ]);

        // Reconvert
        return $this->convertPdfToBraille($material);
    }
}
