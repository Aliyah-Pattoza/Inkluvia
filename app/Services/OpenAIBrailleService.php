<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIBrailleService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Convert text to Braille using OpenAI GPT-4
     */
    public function convertTextToBraille($text, $context = [])
    {
        try {
            $prompt = $this->buildBraillePrompt($text, $context);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->apiUrl, [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert in Braille conversion. Convert text to Grade 2 Braille (Indonesian Braille standard) with proper formatting for mathematical symbols, punctuation, and special characters. Return only the Braille Unicode characters.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 4000
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $brailleText = $result['choices'][0]['message']['content'] ?? '';
                
                // Clean and validate Braille output
                $brailleText = $this->cleanBrailleOutput($brailleText);
                
                return [
                    'success' => true,
                    'braille' => $brailleText,
                    'original' => $text
                ];
            }

            Log::error('OpenAI API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'API request failed',
                'braille' => $this->fallbackBrailleConversion($text)
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI Braille conversion error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'braille' => $this->fallbackBrailleConversion($text)
            ];
        }
    }

    /**
     * Build comprehensive prompt for Braille conversion
     */
    private function buildBraillePrompt($text, $context)
    {
        $contextInfo = '';
        if (!empty($context['kategori'])) {
            $contextInfo .= "Category: {$context['kategori']}\n";
        }
        if (!empty($context['tingkat'])) {
            $contextInfo .= "Education Level: {$context['tingkat']}\n";
        }

        return <<<PROMPT
Convert the following text to Indonesian Braille (Grade 2) using proper Unicode Braille patterns (U+2800 to U+28FF).

{$contextInfo}

Rules:
1. Use proper Braille letters for Indonesian alphabet (a-z)
2. Convert numbers using numeric indicator (⠼) followed by letters a-j
3. Mathematical operators: + (⠖), - (⠤), × (⠦), ÷ (⠲), = (⠶)
4. Punctuation: . (⠲), , (⠂), ? (⠦⠄), ! (⠖⠄), : (⠒), ; (⠆)
5. Brackets: ( (⠐⠣), ) (⠐⠜), [ (⠨⠣), ] (⠨⠜)
6. Preserve line breaks and paragraph structure
7. Capital indicator: ⠠ before capital letter
8. Use space (⠀) between words

Text to convert:
{$text}

Return ONLY the Braille Unicode characters, maintaining the original structure.
PROMPT;
    }

    /**
     * Clean and validate Braille output
     */
    private function cleanBrailleOutput($brailleText)
    {
        // Remove any non-Braille characters except newlines and spaces
        $brailleText = preg_replace('/[^\x{2800}-\x{28FF}\s\n]/u', '', $brailleText);
        
        // Normalize whitespace
        $brailleText = preg_replace('/[ \t]+/u', '⠀', $brailleText);
        
        return trim($brailleText);
    }

    /**
     * Fallback Braille conversion (basic character mapping)
     */
    private function fallbackBrailleConversion($text)
    {
        $brailleMap = [
            // Lowercase letters
            'a' => '⠁', 'b' => '⠃', 'c' => '⠉', 'd' => '⠙', 'e' => '⠑',
            'f' => '⠋', 'g' => '⠛', 'h' => '⠓', 'i' => '⠊', 'j' => '⠚',
            'k' => '⠅', 'l' => '⠇', 'm' => '⠍', 'n' => '⠝', 'o' => '⠕',
            'p' => '⠏', 'q' => '⠟', 'r' => '⠗', 's' => '⠎', 't' => '⠞',
            'u' => '⠥', 'v' => '⠧', 'w' => '⠺', 'x' => '⠭', 'y' => '⠽', 'z' => '⠵',
            
            // Numbers (with numeric indicator)
            '1' => '⠼⠁', '2' => '⠼⠃', '3' => '⠼⠉', '4' => '⠼⠙', '5' => '⠼⠑',
            '6' => '⠼⠋', '7' => '⠼⠛', '8' => '⠼⠓', '9' => '⠼⠊', '0' => '⠼⠚',
            
            // Punctuation
            '.' => '⠲', ',' => '⠂', '?' => '⠦⠄', '!' => '⠖⠄',
            ':' => '⠒', ';' => '⠆', '-' => '⠤',
            
            // Math operators
            '+' => '⠖', '×' => '⠦', '÷' => '⠲', '=' => '⠶',
            
            // Brackets
            '(' => '⠐⠣', ')' => '⠐⠜',
            '[' => '⠨⠣', ']' => '⠨⠜',
            
            // Space
            ' ' => '⠀'
        ];

        $result = '';
        $length = mb_strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1);
            $lower = mb_strtolower($char);
            
            // Handle uppercase
            if ($char !== $lower && isset($brailleMap[$lower])) {
                $result .= '⠠' . $brailleMap[$lower]; // Capital indicator + letter
            } elseif (isset($brailleMap[$lower])) {
                $result .= $brailleMap[$lower];
            } elseif ($char === "\n") {
                $result .= "\n";
            } else {
                $result .= '⠿'; // Unknown character
            }
        }

        return $result;
    }

    /**
     * Convert entire JSON structure to Braille
     */
    public function convertJsonToBraille($jsonData)
    {
        $brailleJson = [
            'judul' => '',
            'penerbit' => '',
            'tahun' => '',
            'edisi' => '',
            'pages' => []
        ];

        // Convert metadata
        if (isset($jsonData['judul'])) {
            $result = $this->convertTextToBraille($jsonData['judul']);
            $brailleJson['judul'] = $result['braille'];
        }
        
        if (isset($jsonData['penerbit'])) {
            $result = $this->convertTextToBraille($jsonData['penerbit']);
            $brailleJson['penerbit'] = $result['braille'];
        }
        
        if (isset($jsonData['tahun'])) {
            $result = $this->convertTextToBraille((string)$jsonData['tahun']);
            $brailleJson['tahun'] = $result['braille'];
        }
        
        if (isset($jsonData['edisi'])) {
            $result = $this->convertTextToBraille($jsonData['edisi']);
            $brailleJson['edisi'] = $result['braille'];
        }

        // Convert pages
        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            foreach ($jsonData['pages'] as $page) {
                $braillePage = [
                    'page' => $page['page'] ?? 1,
                    'lines' => []
                ];

                if (isset($page['lines']) && is_array($page['lines'])) {
                    foreach ($page['lines'] as $line) {
                        if (isset($line['text']) && !empty(trim($line['text']))) {
                            $result = $this->convertTextToBraille($line['text']);
                            $braillePage['lines'][] = [
                                'line' => $line['line'] ?? 1,
                                'text' => $result['braille'],
                                'original' => $line['text']
                            ];
                        }
                    }
                }

                $brailleJson['pages'][] = $braillePage;
            }
        }

        return $brailleJson;
    }
}