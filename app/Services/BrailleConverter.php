<?php

namespace App\Services;

class BrailleConverter
{
    private array $letterMap = [
        'a'=>'⠁','b'=>'⠃','c'=>'⠉','d'=>'⠙','e'=>'⠑','f'=>'⠋','g'=>'⠛','h'=>'⠓','i'=>'⠊','j'=>'⠚',
        'k'=>'⠅','l'=>'⠇','m'=>'⠍','n'=>'⠝','o'=>'⠕','p'=>'⠏','q'=>'⠟','r'=>'⠗','s'=>'⠎','t'=>'⠞',
        'u'=>'⠥','v'=>'⠧','w'=>'⠺','x'=>'⠭','y'=>'⠽','z'=>'⠵'
    ];


    private array $digitCell = ['0'=>'⠚','1'=>'⠁','2'=>'⠃','3'=>'⠉','4'=>'⠙','5'=>'⠑','6'=>'⠋','7'=>'⠛','8'=>'⠓','9'=>'⠊'];
    private string $numberSign = '⠼';
    private string $space = '⠀'; 

    private array $punct = [
        ','=>'⠂','.'=>'⠲',':'=>'⠒',';'=>'⠆','!'=>'⠖','?'=>'⠦',
        '('=>'⠶',')'=>'⠶','['=>'⠨⠣',']'=>'⠨⠜',
        '"open'=>'⠘⠦','"close'=>'⠘⠴',"'"=>'⠄',
        '/'=>'⠌','\\'=>'⠸⠡','#'=>'⠼','&'=>'⠯','*'=>'⠔⠔','+'=>'⠢','='=>'⠒⠒','-'=>'⠔','_'=>'⠨⠤',
        '@'=>'⠈⠁', '"'=>'⠦', '"'=>'⠴' 
    ];

    private array $mathOps = [
        '+' => '⠐⠢',
        '−' => '⠐⠔', 
        '×' => '⠐⠡', 
        '÷' => '⠐⠌⠌',
        '±' => '⠢⠔',
        '∗' => '⠐⠡', 
        '⋅' => '⠐⠲', 
        '∘' => '⠴',
        '∼' => '⠂⠒', 

        '=' => '⠶', 
        '≠' => '⠶⠈⠂',
        '≤' => '⠤⠈⠣',
        '>' => '⠒⠉', 
        '≥' => '⠤⠈⠜',
        '≈' => '⠘⠔',
        '≡' => '⠤⠶',
        
        '∈' => '⠘⠑',
        '∉' => '⠘⠑⠈⠂',
        '∋' => '⠈⠘⠑',
        '∌' => '⠈⠘⠑⠈⠂',
        '⊂' => '⠘⠣',
        '⊃' => '⠘⠜',
        '⊄' => '⠘⠣⠈⠂',
        '⊅' => '⠘⠜⠈⠂',
        '⊆' => '⠤⠘⠣',
        '⊇' => '⠤⠘⠜',
        '⊈' => '⠤⠘⠣⠈⠂',
        '⊉' => '⠤⠘⠜⠈⠂',
        '⊊' => '⠄⠘⠣',
        '⊋' => '⠄⠘⠜', 
        '∩' => '⠨⠇',
        '∪' => '⠨⠥', 
        '∖' => '⠨⠡', 
        '∅' => '⠈⠚',
        
        '∧' => '⠦',
        '∨' => '⠧',
        '¬' => '⠈⠦',
        '∀' => '⠘⠁', 
        '∃' => '⠘⠑', 
        '∄' => '⠘⠑⠈⠂', 
        '∴' => '⠄⠔',
        '∵' => '⠈⠌',
        
        '∂' => '⠈⠙',
        '∇' => '⠘⠙',
        '∫' => '⠖',
        '∮' => '⠈⠖',
        '∑' => '⠎',
        '∏' => '⠏',
        '∞' => '⠼⠶',
        '√' => '⠶⠄',
        '∎' => '⠤⠼⠙',
        
        '∠' => '⠤⠈',
        '∡' => '⠄⠤⠈',
        '⊾' => '⠼⠤⠈',
        '∥' => '⠼⠇',
        '∦' => '⠼⠇⠈⠂',
        '⟂' => '⠼⠤',
        '⊥' => '⠼⠤',
        
        '→' => '⠒',
        '←' => '⠒',
        '↑' => '⠘⠒',
        '↓' => '⠄⠒',
        '↔' => '⠒⠒',
        '↕' => '⠘⠒⠄⠒',
        '⇒' => '⠒⠒',
        '⇐' => '⠒⠒',
        '⇔' => '⠒⠒⠒',
        '⇑' => '⠘⠒⠒',
        '⇓' => '⠄⠒⠒',
    ];

    private array $greekLower = [
        'α' => '⠄⠁', 'β' => '⠄⠃', 'γ' => '⠄⠛', 'δ' => '⠄⠙', 'ε' => '⠄⠑',
        'ζ' => '⠄⠵', 'η' => '⠄⠂', 'θ' => '⠄⠦', 'ι' => '⠄⠊', 'κ' => '⠄⠅',
        'λ' => '⠄⠇', 'μ' => '⠄⠍', 'ν' => '⠄⠝', 'ξ' => '⠄⠭', 'ο' => '⠄⠕',
        'π' => '⠄⠏', 'ρ' => '⠄⠗', 'σ' => '⠄⠎', 'τ' => '⠄⠞', 'υ' => '⠄⠥',
        'φ' => '⠄⠋', 'χ' => '⠄⠯', 'ψ' => '⠄⠽', 'ω' => '⠄⠺'
    ];

    private array $greekUpper = [
        'Α' => '⠄⠄⠁', 'Β' => '⠄⠄⠃', 'Γ' => '⠄⠄⠛', 'Δ' => '⠄⠄⠙', 'Ε' => '⠄⠄⠑',
        'Ζ' => '⠄⠄⠵', 'Η' => '⠄⠄⠂', 'Θ' => '⠄⠄⠦', 'Ι' => '⠄⠄⠊', 'Κ' => '⠄⠄⠅',
        'Λ' => '⠄⠄⠇', 'Μ' => '⠄⠄⠍', 'Ν' => '⠄⠄⠝', 'Ξ' => '⠄⠄⠭', 'Ο' => '⠄⠄⠕',
        'Π' => '⠄⠄⠏', 'Ρ' => '⠄⠄⠗', 'Σ' => '⠄⠄⠎', 'Τ' => '⠄⠄⠞', 'Υ' => '⠄⠄⠥',
        'Φ' => '⠄⠄⠋', 'Χ' => '⠄⠄⠯', 'Ψ' => '⠄⠄⠽', 'Ω' => '⠄⠄⠺'
    ];

    private array $specialMath = [
        'µ' => '⠄⠍',
        'Ω' => '⠄⠄⠺',
        '∆' => '⠄⠄⠙',
        '∏' => '⠄⠄⠏',
        '∑' => '⠄⠄⠎',
        'Å' => '⠄⠘⠁',
        '°' => '⠘⠚',
        '′' => '⠛',
        '″' => '⠛⠛',
        '‴' => '⠛⠛⠛',
        '…' => '⠐⠐⠐',
        '†' => '⠈⠄⠦',
        '‡' => '⠈⠄⠻',
        '¢' => '⠈⠉',
        '€' => '⠈⠑',
        '£' => '⠈⠇',
        '$' => '⠈⠎',
        '¥' => '⠈⠽',
        '¦' => '⠄⠸',
        '∣' => '⠤⠸',
        '∤' => '⠤⠸⠈⠂',
        '⊦' => '⠤⠂',
        '⊣' => '⠈⠤⠂',
        '⊨' => '⠘⠤⠂',
        '⊬' => '⠤⠂⠈⠂',
        '⊭' => '⠘⠤⠂⠈⠂',
    ];

    private array $brackets = [
        '('=>'⠶',')'=>'⠶',
        '[' => '⠄⠶⠣', ']' => '⠄⠶⠜',
        '{' => '⠤⠶⠣', '}' => '⠤⠶⠜',
        '〈' => '⠈⠶⠣', '〉' => '⠈⠶⠜',
        '|' => '⠤⠸',
    ];

    private array $indicators = [
        '#' => '⠼',
        '##' => '⠼⠼',
        "#'" => '⠼⠄',
        ";'" => '⠂⠄',
        ',' => '⠠',
    ];

    private string $superscript = '⠘';
    private string $rootSign    = '⠶⠄';

    public function toBraille(string $src): string
    {

        $out = $this->convertMathFunctions($src);
        $out = $this->convertMathConstants($out);

        $out = $this->convertRoots($out);

        $out = $this->convertExponents($out);

        $out = $this->convertFractions($out);

        $out = $this->insertMulBetweenNumberAndVariable($out);

        $out = $this->applyUebMathRules($out);

        $out = $this->convertNumericRuns($out);


        $out = $this->mapChars($out);

        $out = $this->postTidy($out);

        return $out;
    }


    private function convertRoots(string $s): string
    {
        $s = preg_replace('/√/u', $this->rootSign, $s);
        return $s;
    }

    private function applyUebMathRules(string $s): string
    {
        $s = $this->addGrade1Indicators($s);
        $s = $this->applyMathSpacing($s);
        $s = $this->applyMathGrouping($s);
        
        return $s;
    }

    private function addGrade1Indicators(string $s): string
    {
        $grade1Symbols = [
            'and' => '∧', 'or' => '∨', 'the' => '∂', 'of' => '∅', 'with' => '∋',
            'for' => '∀', 'this' => '∴', 'be' => '∵', 'were' => '∃', 'was' => '∄'
        ];
        foreach ($grade1Symbols as $word => $symbol) {
            $s = str_replace($symbol, '⠂' . $symbol, $s);
        }
        
        return $s;
    }

    private function applyMathSpacing(string $s): string
    {
        $comparisonSigns = ['=', '≠', '<', '≤', '>', '≥', '≪', '≫', '≮', '≯', '≰', '≱', '∝', '≃', '≅', '≈', '≏', '≑', '≡', '≢'];
        
        foreach ($comparisonSigns as $sign) {
            $s = preg_replace('/(?<!\s)' . preg_quote($sign, '/') . '(?!\s)/u', ' ' . $sign . ' ', $s);
        }
        
        $operationSigns = ['+', '−', '×', '÷', '±', '∓', '∗', '∘', '∼', '⋅', '∧', '∨', '∩', '∪', '∖'];
        
        foreach ($operationSigns as $sign) {
            $s = preg_replace('/\s*' . preg_quote($sign, '/') . '\s*/u', $sign, $s);
        }
        
        return $s;
    }

    private function applyMathGrouping(string $s): string
    {
        $s = preg_replace('/\(/u', '⠶⠣', $s);
        $s = preg_replace('/\)/u', '⠶⠜', $s);
        $s = preg_replace('/\[/u', '⠄⠶⠣', $s);
        $s = preg_replace('/\]/u', '⠄⠶⠜', $s);
        $s = preg_replace('/\{/u', '⠤⠶⠣', $s);
        $s = preg_replace('/\}/u', '⠤⠶⠜', $s);
        
        return $s;
    }

    private function convertExponents(string $s): string
    {
        $s = preg_replace_callback('/\^(\(?[-+]?\w+\)?)/u', function($m) {
            $exp = $m[1];
            if ($exp[0] === '(' && substr($exp,-1) === ')') {
                $exp = substr($exp,1,-1);
            }
            return $this->superscript . $exp;
        }, $s);
        return $s;
    }

    private function convertMathFunctions(string $s): string
    {
        $functions = [
            'sin' => '⠎⠊⠝',
            'cos' => '⠉⠕⠎',
            'tan' => '⠞⠁⠝',
            'log' => '⠇⠕⠛',
            'ln' => '⠇⠝',
            'exp' => '⠑⠭⠏',
            'sqrt' => '⠎⠟⠗⠞',
            'abs' => '⠁⠃⠎',
            'max' => '⠍⠁⠭',
            'min' => '⠍⠊⠝',
            'lim' => '⠇⠊⠍',
            'sum' => '⠎⠥⠍',
            'prod' => '⠏⠗⠕⠙',
            'int' => '⠊⠝⠞',
            'diff' => '⠙⠊⠋⠋',
            'grad' => '⠛⠗⠁⠙',
            'div' => '⠙⠊⠧',
            'curl' => '⠉⠥⠗⠇',
            'det' => '⠙⠑⠞',
            'rank' => '⠗⠁⠝⠅',
            'trace' => '⠞⠗⠁⠉⠑',
            'norm' => '⠝⠕⠗⠍',
            'dim' => '⠙⠊⠍',
            'span' => '⠎⠏⠁⠝',
            'ker' => '⠅⠑⠗',
            'im' => '⠊⠍',
            'Re' => '⠗⠑',
            'Im' => '⠊⠍',
            'arg' => '⠁⠗⠛',
            'mod' => '⠍⠕⠙',
            'gcd' => '⠛⠉⠙',
            'lcm' => '⠇⠉⠍',
        ];
        
        foreach ($functions as $func => $braille) {
            $s = preg_replace('/\b' . preg_quote($func, '/') . '\b/u', '⠂' . $braille, $s);
        }
        
        return $s;
    }

    private function convertMathConstants(string $s): string
    {
        $constants = [
            'π' => '⠄⠏', // pi
            'e' => '⠑', // euler's number
            'i' => '⠊', // imaginary unit
            '∞' => '⠼⠶', // infinity
            'φ' => '⠄⠋', // golden ratio (phi)
            'γ' => '⠄⠛', // euler-mascheroni constant
            'ζ' => '⠄⠵', // riemann zeta function
        ];
        
        foreach ($constants as $const => $braille) {
            $s = str_replace($const, $braille, $s);
        }
        
        return $s;
    }

    private function convertFractions(string $s): string
    {
  
        $s = preg_replace('/(\d+)\s*:\s*(\d+)/u', '$1 / $2', $s);
        

        return $s;
    }

    private function insertMulBetweenNumberAndVariable(string $s): string
    {

        $s = preg_replace('/(\d)([a-jA-J])/u', '$1*$2', $s);
        return $s;
    }

    private function convertNumericRuns(string $s): string
    {
        // One ⠼ per continuous run of digits (optionally with a decimal separator).
        // Example: 123 -> ⠼⠁⠃⠉ ; 12,34 or 12.34 -> ⠼⠁⠃⠲⠉⠙ (using period cell for dot)
        return preg_replace_callback('/(?<!\w)(\d+(?:[.,]\d+)*)/u', function($m) {
            $run = $m[1];
            // Convert decimal comma to braille period cell; keep only digits and dot for mapping
            $run = str_replace(',', '.', $run);
            $out = $this->numberSign;
            for ($i=0; $i<strlen($run); $i++) {
                $ch = $run[$i];
                if ($ch === '.') {
                    $out .= '⠲'; // braille period cell for decimal point (per examples)
                } else {
                    $out .= $this->digitCell[$ch] ?? $ch;
                }
            }
            return $out;
        }, $s);
    }

    private function mapChars(string $s): string
    {
        $out = '';
        $len = mb_strlen($s, 'UTF-8');
        $i = 0;
        
        while ($i < $len) {
            $ch = mb_substr($s, $i, 1, 'UTF-8');
            $lower = mb_strtolower($ch, 'UTF-8');
            
            // Check for multi-character sequences first
            $mapped = $this->checkMultiCharSequence($s, $i);
            if ($mapped !== null) {
                $out .= $mapped['braille'];
                $i += $mapped['length'];
                continue;
            }
            
            
            // Check Greek letters (uppercase first, then lowercase)
            if (isset($this->greekUpper[$ch])) {
                $out .= $this->greekUpper[$ch];
            } elseif (isset($this->greekLower[$ch])) {
                $out .= $this->greekLower[$ch];
            }
            // Check mathematical operations
            elseif (isset($this->mathOps[$ch])) {
                $out .= $this->mathOps[$ch];
            }
            // Check special mathematical symbols
            elseif (isset($this->specialMath[$ch])) {
                $out .= $this->specialMath[$ch];
            }
            // Check brackets
            elseif (isset($this->brackets[$ch])) {
                $out .= $this->brackets[$ch];
            }
            // Check regular letters
            elseif (isset($this->letterMap[$lower])) {
                $out .= $this->letterMap[$lower];
            }
            // Check punctuation
            elseif (isset($this->punct[$ch])) {
                $out .= $this->punct[$ch];
            }
            // Check indicators
            elseif (isset($this->indicators[$ch])) {
                $out .= $this->indicators[$ch];
            }
            // Handle spaces
            elseif ($ch === ' ') {
                $out .= $this->space;
            }
            // Handle newlines
            elseif ($ch === "\n") {
                $out .= "\n";
            }
            // Handle tabs
            elseif ($ch === "\t") {
                $out .= $this->space . $this->space; // Two spaces for tab
            }
            else {
                // Pass through unknowns (or handle more symbols here)
                $out .= $ch;
            }
            
            $i++;
        }
        
        return $out;
    }
    
    private function checkMultiCharSequence(string $s, int $start): ?array
    {
        $remaining = mb_substr($s, $start);
        
        $sequences = [
            '##' => ['⠼⠼', 2], // numeric passage indicator
            "#'" => ['⠼⠄', 2], // numeric passage terminator
            './' => ['⠄⠌', 2], // general fraction line
            '.9' => ['⠄⠘', 2], // directly above
            '.5' => ['⠄⠐', 2], // directly below
            '""' => ['⠶⠶', 2], // line continuation with space
            ';;' => ['⠂⠂', 2], // grade 1 word indicator
            ';;;' => ['⠂⠂⠂', 3], // grade 1 passage indicator
            ";'" => ['⠂⠄', 2], // grade 1 terminator
            ',,' => ['⠄⠄', 2], // capital word indicator
            ',,,' => ['⠄⠄⠄', 3], // capital passage indicator
            ",'" => ['⠄⠄', 2], // capital terminator
            '^1' => ['⠘⠁', 2], // bold word indicator
            '^2' => ['⠘⠃', 2], // bold symbol indicator
            '^7' => ['⠘⠛', 2], // bold passage indicator
            "^'" => ['⠘⠄', 2], // bold terminator
            '@1' => ['⠈⠁', 2], // script word indicator
            '@2' => ['⠈⠃', 2], // script symbol indicator
            '@7' => ['⠈⠛', 2], // script passage indicator
            "@'" => ['⠈⠄', 2],
        ];
        
        foreach ($sequences as $seq => $data) {
            if (mb_substr($remaining, 0, mb_strlen($seq)) === $seq) {
                return ['braille' => $data[0], 'length' => $data[1]];
            }
        }
        
        return null;
    }



    private function postTidy(string $s): string
    {
        $s = preg_replace('/⠼⠼+/', '⠼', $s); 
        return $s;
    }

    public function convertLine(string $text): string
    {
        if (empty(trim($text))) {
            return '';
        }
        
        return $this->toBraille(trim($text));
    }

    public function convertLines(array $lines): array
    {
        $brailleLines = [];
        
        foreach ($lines as $line) {
            if (isset($line['text']) && !empty(trim($line['text']))) {
                $brailleLines[] = [
                    'line' => $line['line'] ?? count($brailleLines) + 1,
                    'text' => $this->convertLine($line['text']),
                    'original_text' => $line['text']
                ];
            }
        }
        
        return $brailleLines;
    }

    public function convertPage(array $pageData): array
    {
        $braillePage = [
            'page' => $pageData['page'] ?? 1,
            'lines' => []
        ];
        
        if (isset($pageData['lines']) && is_array($pageData['lines'])) {
            $braillePage['lines'] = $this->convertLines($pageData['lines']);
        }
        
        return $braillePage;
    }

    public function convertDocument(array $documentData): array
    {
        $brailleDocument = [
            'judul' => $documentData['judul'] ?? 'Untitled',
            'penerbit' => $documentData['penerbit'] ?? '',
            'tahun' => $documentData['tahun'] ?? '',
            'edisi' => $documentData['edisi'] ?? '',
            'pages' => []
        ];
        
        if (isset($documentData['pages']) && is_array($documentData['pages'])) {
            foreach ($documentData['pages'] as $pageData) {
                $brailleDocument['pages'][] = $this->convertPage($pageData);
            }
        }
        
        return $brailleDocument;
    }
}
