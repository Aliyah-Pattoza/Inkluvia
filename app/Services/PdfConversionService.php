<?php

namespace App\Services;

use App\Models\Material;
use App\Models\BrailleContent;
use App\Models\MaterialBrailleContent;
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

            // Step 1: Read existing JSON data (created by PdfToJsonService)
            $jsonData = $this->readJsonData($material->file_path);
            
            if (!$jsonData) {
                throw new Exception('Failed to read JSON data from file: ' . $material->file_path);
            }

            // Step 2: Convert text to Braille
            $brailleContent = $this->convertTextToBraille($jsonData);

            // Step 3: Save Braille content
            $brailleDataPath = $this->saveBrailleContent($material, $brailleContent);

            // Step 4: Update material record
            $pageCount = isset($brailleContent['pages']) ? count($brailleContent['pages']) : count($brailleContent);
            $material->update([
                'braille_data_path' => $brailleDataPath,
                'total_halaman' => $pageCount,
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
     * Read existing JSON data from file
     */
    private function readJsonData($filePath)
    {
        if (!Storage::disk('private')->exists($filePath)) {
            throw new Exception('JSON file not found: ' . $filePath);
        }

        $jsonContent = Storage::disk('private')->get($filePath);
        $jsonData = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON file: ' . json_last_error_msg());
        }

        return $jsonData;
    }

    /**
     * Convert extracted text to Braille
     */
    private function convertTextToBraille($jsonData)
    {
        // Return the full JSON structure with braille content
        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            $brailleJson = [
                'judul' => $this->placeholderBrailleConversion($jsonData['judul'] ?? ''),
                'penerbit' => $this->placeholderBrailleConversion($jsonData['penerbit'] ?? ''),
                'tahun' => $this->placeholderBrailleConversion($jsonData['tahun'] ?? ''),
                'edisi' => $this->placeholderBrailleConversion($jsonData['edisi'] ?? ''),
                'pages' => []
            ];
            
            foreach ($jsonData['pages'] as $pageData) {
                $braillePage = $this->convertPageToBraille($pageData);
                $brailleJson['pages'][] = $braillePage;
            }
            
            return $brailleJson;
        } else {
            // Fallback for old structure
            $brailleContent = [];
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
    }

    /**
     * Extract text from page data structure
     */
    private function extractTextFromPage($pageData)
    {
        $text = '';
        
        if (isset($pageData['lines']) && is_array($pageData['lines'])) {
            foreach ($pageData['lines'] as $line) {
                if (isset($line['text'])) {
                    $text .= $line['text'] . "\n";
                }
            }
        } elseif (isset($pageData['text'])) {
            $text = $pageData['text'];
        }
        
        return trim($text);
    }

    /**
     * Convert a single page to Braille
     */
    private function convertPageToBraille($pageData)
    {
        $braillePage = [
            'page' => $pageData['page'] ?? 1,
            'lines' => []
        ];
        
        if (isset($pageData['lines']) && is_array($pageData['lines'])) {
            foreach ($pageData['lines'] as $index => $line) {
                if (isset($line['text']) && !empty(trim($line['text']))) {
                    $brailleResult = $this->placeholderBrailleConversion($line['text'], true);
                    $braillePage['lines'][] = [
                        'line' => $index + 1,
                        'text' => $brailleResult['text'],
                        'decimal' => $brailleResult['decimal']
                    ];
                }
            }
        }
        
        return $braillePage;
    }

    /**
     * Placeholder Braille conversion (NOT real Braille)
     * Replace this with actual Braille conversion logic
     */
    private function placeholderBrailleConversion($text, bool $withDecimal = false)
    {
        // This is just a placeholder - NOT actual Braille conversion
        // You need to implement real Braille conversion here
        
        // Convert to string if it's not already
        $text = (string) $text;
        
        if (empty($text)) {
            return $withDecimal ? ['text' => '', 'decimal' => ''] : '';
        }
        
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

        $brailleChars = [];
        $decimalParts = [];

        $length = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $char = mb_strtolower($char, 'UTF-8');

            $brailleChar = $brailleMap[$char] ?? '⠿'; // Unknown character symbol
            $brailleChars[] = $brailleChar;

            if ($withDecimal) {
                $decimalParts[] = $this->convertBrailleCharToDecimalString($brailleChar);
            }
        }

        $brailleString = implode('', $brailleChars);

        if (!$withDecimal) {
            return $brailleString;
        }

        return [
            'text' => $brailleString,
            'decimal' => implode('', $decimalParts)
        ];
    }

    private function convertBrailleCharToDecimalString(string $brailleChar): string
    {
        $codePoint = $this->getCodePoint($brailleChar);

        if ($codePoint === null || $codePoint < 0x2800 || $codePoint > 0x28FF) {
            return '63';
        }

        $mask = $codePoint - 0x2800;
        $mask &= 0b00111111;

        $binary = '';
        for ($i = 0; $i < 6; $i++) {
            $binary .= ($mask & (1 << $i)) ? '1' : '0';
        }

        $decimal = bindec($binary);

        return str_pad((string) $decimal, 2, '0', STR_PAD_LEFT);
    }

    private function getCodePoint(string $char): ?int
    {
        if ($char === '') {
            return null;
        }

        $encoded = mb_convert_encoding($char, 'UCS-4BE', 'UTF-8');
        if ($encoded === false) {
            return null;
        }

        $codePoint = unpack('N', $encoded);

        return $codePoint[1] ?? null;
    }
    
    /**
     * Convert single line of text to Braille using the BrailleConverter
     */
    private function convertLineToBraille($text)
    {
        // Preprocess mathematical content
        $text = $this->preprocessMathContent($text);
        
        return $this->brailleConverter->convertLine($text);
    }

    /**
     * Preprocess mathematical content for better conversion
     */
    private function preprocessMathContent($text)
    {
        // Normalize mathematical symbols
        $text = $this->normalizeMathSymbols($text);
        
        // Handle mathematical expressions
        $text = $this->handleMathExpressions($text);
        
        // Clean up spacing around mathematical symbols
        $text = $this->cleanMathSpacing($text);
        
        return $text;
    }

    /**
     * Normalize mathematical symbols to standard Unicode
     */
    private function normalizeMathSymbols($text)
    {
        $normalizations = [
            // Common ASCII alternatives to proper mathematical symbols
            '!=' => '≠',
            '<=' => '≤',
            '>=' => '≥',
            '~=' => '≈',
            '+-' => '±',
            '-+' => '∓',
            '*' => '×', // when used as multiplication
            '/' => '÷', // when used as division
            '^' => '^', // keep as is for exponents
            'sqrt' => '√',
            'inf' => '∞',
            'pi' => 'π',
            'alpha' => 'α',
            'beta' => 'β',
            'gamma' => 'γ',
            'delta' => 'δ',
            'epsilon' => 'ε',
            'theta' => 'θ',
            'lambda' => 'λ',
            'mu' => 'μ',
            'sigma' => 'σ',
            'phi' => 'φ',
            'omega' => 'ω',
        ];
        
        foreach ($normalizations as $ascii => $unicode) {
            $text = str_replace($ascii, $unicode, $text);
        }
        
        return $text;
    }

    /**
     * Handle mathematical expressions and equations
     */
    private function handleMathExpressions($text)
    {
        // Handle common mathematical patterns
        
        // Fractions: a/b -> a÷b
        $text = preg_replace('/(\w+)\/(\w+)/u', '$1÷$2', $text);
        
        // Powers: x^2 -> x^2 (keep as is)
        // This is already handled by the BrailleConverter
        
        // Square roots: sqrt(x) -> √x
        $text = preg_replace('/sqrt\s*\(([^)]+)\)/u', '√$1', $text);
        
        // Mathematical functions
        $functions = ['sin', 'cos', 'tan', 'log', 'ln', 'exp', 'abs', 'max', 'min', 'lim', 'sum', 'prod', 'int'];
        foreach ($functions as $func) {
            $text = preg_replace('/\b' . $func . '\s*\(/u', $func . '(', $text);
        }
        
        return $text;
    }

    /**
     * Clean up spacing around mathematical symbols
     */
    private function cleanMathSpacing($text)
    {
        // Remove extra spaces around mathematical operators
        $text = preg_replace('/\s*([+\-×÷=<>≤≥≠±∓])\s*/u', '$1', $text);
        
        // Ensure proper spacing around comparison operators
        $text = preg_replace('/([^=])(=)([^=])/u', '$1 $2 $3', $text);
        $text = preg_replace('/([^<])([<≥≤])([^=])/u', '$1 $2 $3', $text);
        $text = preg_replace('/([^>])([>≤≥])([^=])/u', '$1 $2 $3', $text);
        
        return $text;
    }

    /**
     * Save Braille content to database and file
     */
    private function saveBrailleContent(Material $material, $brailleContent)
    {
        // Handle new JSON structure
        if (isset($brailleContent['pages']) && is_array($brailleContent['pages'])) {
            // New structure with full JSON
            foreach ($brailleContent['pages'] as $pageData) {
                $brailleText = '';
                $originalText = '';
                $lineCount = 0;
                $characterCount = 0;
                
                if (isset($pageData['lines']) && is_array($pageData['lines'])) {
                    foreach ($pageData['lines'] as $line) {
                        $brailleText .= $line['text'] . "\n";
                        $characterCount += strlen($line['text']);
                    }
                    $lineCount = count($pageData['lines']);
                }
                
                MaterialBrailleContent::create([
                    'material_id' => $material->id,
                    'page_number' => $pageData['page'] ?? 1,
                    'braille_text' => trim($brailleText),
                    'original_text' => $originalText,
                    'metadata' => [
                        'judul' => $brailleContent['judul'] ?? '',
                        'penerbit' => $brailleContent['penerbit'] ?? '',
                        'tahun' => $brailleContent['tahun'] ?? '',
                        'edisi' => $brailleContent['edisi'] ?? ''
                    ],
                    'line_count' => $lineCount,
                    'character_count' => $characterCount
                ]);
            }
        } else {
            // Old structure fallback
            foreach ($brailleContent as $pageData) {
                MaterialBrailleContent::create([
                    'material_id' => $material->id,
                    'page_number' => $pageData['page_number'],
                    'braille_text' => $pageData['content']['braille_text'],
                    'original_text' => $pageData['original_text'],
                    'metadata' => $pageData['metadata'],
                    'line_count' => $pageData['content']['line_count'],
                    'character_count' => $pageData['content']['character_count']
                ]);
            }
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
        $brailleContents = MaterialBrailleContent::where('material_id', $material->id)->get();
        
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
        MaterialBrailleContent::where('material_id', $material->id)->delete();
        
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
<<<<<<< HEAD
}
=======
}
>>>>>>> a5df58d (update fitur user)
