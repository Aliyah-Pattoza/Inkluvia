@extends('layouts.user')

@section('title', 'Kirim Materi Braille')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('user.jadwal-belajar') }}" 
           class="inline-flex items-center text-primary hover:text-primary-dark font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded px-2 py-1"
           aria-label="Kembali ke daftar jadwal">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
            Kembali ke Jadwal
        </a>
    </div>

    <!-- Session Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $jadwal->judul }}</h1>
                <p class="text-sm text-gray-600">{{ $jadwal->materi ?? 'Materi Pembelajaran' }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <i class="fas fa-circle text-xs mr-2 animate-pulse" aria-hidden="true"></i>
                Sedang Berlangsung
            </span>
        </div>
    </div>

    <!-- Braille Display -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6">
        <div class="text-center mb-8">
            <h2 class="text-lg font-semibold text-primary mb-4">Pengenalan Braille</h2>
            
            <!-- Braille Dots -->
            <div id="braille-display" class="mb-6" aria-label="Tampilan pola braille">
                <div id="braille-dots" class="inline-block"></div>
            </div>

            <!-- Character Display -->
            <div id="braille-character" 
                 class="text-6xl font-bold text-gray-900 mb-4"
                 aria-live="polite"
                 aria-atomic="true"></div>

            <!-- Page Info -->
            <div id="page-info" 
                 class="text-gray-600"
                 role="status"
                 aria-live="polite"></div>
        </div>

        <!-- Navigation Controls -->
        <div class="space-y-4">
            <!-- Main Controls -->
            <div class="flex flex-wrap gap-3 justify-center">
                <button id="btn-prev" 
                        class="px-6 py-3 bg-white border-2 border-primary text-primary font-medium rounded-lg hover:bg-primary hover:text-white transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        aria-label="Karakter sebelumnya">
                    <i class="fas fa-chevron-left mr-2" aria-hidden="true"></i>
                    Sebelumnya
                </button>

                <button id="btn-read" 
                        class="px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        aria-label="Baca karakter dengan suara">
                    <i class="fas fa-volume-up mr-2" aria-hidden="true"></i>
                    Baca
                </button>

                <button id="btn-next" 
                        class="px-6 py-3 bg-white border-2 border-primary text-primary font-medium rounded-lg hover:bg-primary hover:text-white transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        aria-label="Karakter selanjutnya">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Line Controls -->
            <div class="flex gap-3 justify-center">
                <button id="btn-line-prev" 
                        class="px-4 py-2 bg-blue-100 text-blue-700 font-medium rounded-lg hover:bg-blue-200 transition-colors focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
                        aria-label="Baris sebelumnya">
                    <i class="fas fa-angle-up" aria-hidden="true"></i>
                    Baris Sebelumnya
                </button>

                <button id="btn-line-next" 
                        class="px-4 py-2 bg-blue-100 text-blue-700 font-medium rounded-lg hover:bg-blue-200 transition-colors focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
                        aria-label="Baris selanjutnya">
                    Baris Selanjutnya
                    <i class="fas fa-angle-down" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Page Controls -->
            <div class="flex gap-3 justify-center">
                <button id="btn-page-prev" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                        aria-label="Halaman sebelumnya">
                    <i class="fas fa-angle-double-left" aria-hidden="true"></i>
                    Halaman Sebelumnya
                </button>

                <button id="btn-page-next" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                        aria-label="Halaman selanjutnya">
                    Halaman Selanjutnya
                    <i class="fas fa-angle-double-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- MQTT Status -->
        <div id="mqtt-status" 
             class="mt-6 p-3 rounded-lg text-center text-sm font-medium"
             role="status"
             aria-live="polite">
            <i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
            Menghubungkan ke MQTT...
        </div>
    </div>

    <!-- Original Text -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Teks Original</h3>
        <div id="original-text" 
             class="text-xl leading-relaxed tracking-wide"
             aria-label="Teks lengkap materi pembelajaran"></div>
    </div>

    <!-- Keyboard Shortcuts Info -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Pintasan Keyboard:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-blue-800">
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">←</kbd> Karakter sebelumnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">→</kbd> Karakter selanjutnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">Space</kbd> Baca karakter</div>
        </div>
    </div>

    <!-- Tombol Selesai Belajar -->
    <div class="mt-6 text-center">
        <form id="complete-session-form" action="{{ route('user.jadwal-belajar.complete', $jadwal) }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" 
                    class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    aria-label="Selesaikan sesi belajar">
                <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                Selesai Belajar
            </button>
        </form>
    </div>
</div>

<!-- Live Region for Screen Reader -->
<div aria-live="assertive" aria-atomic="true" class="sr-only" id="announcements"></div>

@push('styles')
<style>
.braille-dot-row {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.braille-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #1F2937;
}

.braille-dot-empty {
    width: 16px;
    height: 16px;
}

.active-char {
    color: #10B981;
    font-weight: bold;
    background: #D1FAE5;
    border-radius: 4px;
    padding: 2px 8px;
}
</style>
@endpush

@push('scripts')
<!-- MQTT Library -->
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

<script>
// Inject braille data from controller
const brailleData = {
    current_page: {{ $pageNumber }},
    current_line_index: {{ $currentLineIndex }},
    total_pages: {{ $totalPages }},
    total_lines: {{ $totalLines }},
    lines: {!! json_encode($lines) !!},
    material_title: {!! json_encode($material->judul) !!},
    material_description: {!! json_encode($material->deskripsi ?? '') !!},
    current_line_text: {!! json_encode($currentLineText) !!},
    braille_patterns: {!! json_encode($braillePatterns) !!},
    braille_binary_patterns: {!! json_encode($brailleBinaryPatterns) !!},
    braille_decimal_patterns: {!! json_encode($brailleDecimalPatterns) !!}
};

const navigateUrl = {!! json_encode(route('user.jadwal-belajar.navigate', ['jadwal' => $jadwal->id])) !!};
const materialPageUrl = {!! json_encode(route('user.jadwal-belajar.material-page', ['jadwal' => $jadwal->id])) !!};

// MQTT Configuration
const mqttUrl = '{{ config('mqtt.ws_url') }}';
const mqttTopic = '{{ config('mqtt.topic') }}';
const mqttClient = mqtt.connect(mqttUrl, {
    @if(config('mqtt.username'))
    username: '{{ config('mqtt.username') }}',
    @endif
    @if(config('mqtt.password'))
    password: '{{ config('mqtt.password') }}',
    @endif
});

let currentIndex = 0;
let currentPage = brailleData.current_page || 1;
let currentLineIndex = brailleData.current_line_index || 0;
let currentLineText = brailleData.current_line_text || '';
let totalLines = brailleData.total_lines || 1;

// Debug: Log data to console
console.log('Braille Data:', brailleData);
console.log('Space unicode from DB:', brailleData.braille_patterns ? brailleData.braille_patterns[' '] : 'Not found');

// MQTT Connection Handlers
mqttClient.on('connect', function() {
    updateMqttStatus('Terhubung ke MQTT Broker', false);
    mqttClient.subscribe(mqttTopic);
});

mqttClient.on('error', function(err) {
    updateMqttStatus('Kesalahan MQTT: ' + err.message, true);
});

mqttClient.on('offline', function() {
    updateMqttStatus('Koneksi MQTT terputus', true);
});

function updateMqttStatus(message, isError = false) {
    const statusEl = document.getElementById('mqtt-status');
    statusEl.innerHTML = isError 
        ? '<i class="fas fa-exclamation-circle mr-2"></i>' + message
        : '<i class="fas fa-check-circle mr-2"></i>' + message;
    statusEl.className = 'mt-6 p-3 rounded-lg text-center text-sm font-medium ' + 
        (isError ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800');
    
    // Announce to screen reader
    document.getElementById('announcements').textContent = message;
}

// Convert braille binary to decimal for MQTT
function brailleToDecimal(binary6) {
    const reversed = binary6.split('').reverse().join('');
    const first3 = reversed.substring(0, 3);
    const last3 = reversed.substring(3, 6);
    const dec1 = parseInt(first3, 2);
    const dec2 = parseInt(last3, 2);
    return dec1.toString().padStart(1, '0') + dec2.toString().padStart(1, '0');
}

// Update braille unicode pattern
function updateBrailleUnicodePattern(character) {
    const brailleElement = document.getElementById('braille-unicode-pattern');
    if (!brailleElement) {
        console.warn('Element with ID "braille-unicode-pattern" not found');
        return;
    }
    
    const unicodePattern = getBrailleUnicodeForChar(character);
    // For space, show empty cell instead of space unicode
    if (character === ' ') {
        brailleElement.textContent = '⠀'; // Braille blank
        brailleElement.style.background = '#f9f9f9';
        brailleElement.style.border = '1px solid #ddd';
    } else {
        brailleElement.textContent = unicodePattern;
        brailleElement.style.background = '';
        brailleElement.style.border = '';
    }
}

// Get braille unicode for character from database
function getBrailleUnicodeForChar(character) {
    if (character === ' ') {
        return '\u2800'; // Braille blank for space
    }

    if (brailleData.braille_patterns && brailleData.braille_patterns[character]) {
        return brailleData.braille_patterns[character];
    }
    return '\u2800'; // Default to space
}

function getBrailleBinaryForChar(character) {
    if (character === ' ') {
        return '000000';
    }

    if (brailleData.braille_binary_patterns && brailleData.braille_binary_patterns[character]) {
        return brailleData.braille_binary_patterns[character];
    }

    return '000000';
}

function getBrailleDecimalForChar(character) {
    if (character === ' ') {
        return 0;
    }

    if (brailleData.braille_decimal_patterns && typeof brailleData.braille_decimal_patterns[character] !== 'undefined') {
        return brailleData.braille_decimal_patterns[character];
    }

    return 0;
}

function fetchPageData(pageNumber, lineNumber = 1) {
    const safePage = Math.max(1, pageNumber);
    const payload = {
        page: safePage,
        line: Math.max(1, lineNumber)
    };

    return fetch(materialPageUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to fetch page data');
        }
        return response.json();
    })
    .then(result => {
        if (!result.success || !result.data) {
            throw new Error(result.error || 'Invalid response');
        }

        currentPage = result.data.current_page;
        currentLineIndex = result.data.current_line_index;
        totalLines = result.data.total_lines;
        brailleData.lines = result.data.lines;
        brailleData.total_pages = result.data.total_pages;
        currentLineText = result.data.current_line_text || '';

        if (result.data.braille_patterns) {
            brailleData.braille_patterns = result.data.braille_patterns;
        }
        if (result.data.braille_binary_patterns) {
            brailleData.braille_binary_patterns = result.data.braille_binary_patterns;
        }
        if (result.data.braille_decimal_patterns) {
            brailleData.braille_decimal_patterns = result.data.braille_decimal_patterns;
        }

        currentIndex = 0;
        updateView();
    })
    .catch(error => {
        console.error('Error fetching page data:', error);
        updateMqttStatus('Gagal memuat halaman: ' + error.message, true);
    });
}

// Update display
function updateView() {
    console.log('updateView called - currentLineText:', currentLineText);
    
    if (currentLineText && currentLineText.length > 0) {
        const characters = currentLineText.split('');
        currentIndex = Math.max(0, Math.min(currentIndex, characters.length - 1));
        
        const currentChar = characters[currentIndex];
        console.log('Current character:', currentChar);
        console.log('Character code:', currentChar.charCodeAt(0));
        
        const brailleUnicode = getBrailleUnicodeForChar(currentChar);
        console.log('Braille unicode for "' + currentChar + '":', brailleUnicode);
        document.getElementById('braille-dots').innerHTML = brailleUnicode;

        const brailleBinary = getBrailleBinaryForChar(currentChar);
        const brailleDecimal = getBrailleDecimalForChar(currentChar);
        console.log('Braille binary for "' + currentChar + '":', brailleBinary);
        console.log('Braille decimal for "' + currentChar + '":', brailleDecimal);
        
        document.getElementById('braille-character').textContent = currentChar;
        
        document.getElementById('page-info').textContent = 
            `Halaman ${currentPage} • Baris ${currentLineIndex + 1} dari ${totalLines} • Karakter ${currentIndex + 1} dari ${characters.length}`;
        
        document.getElementById('original-text').innerHTML = characters.map((char, i) => {
            return i === currentIndex 
                ? `<span class="active-char">${char}</span>`
                : char;
        }).join('');
        
        updateBrailleUnicodePattern(currentChar);
        
        if (window.mqttClient && mqttClient.connected) {
            try {
                const decimalValue = typeof brailleDecimal !== 'undefined' && brailleDecimal !== null
                    ? String(brailleDecimal)
                    : brailleToDecimal(brailleBinary);
                mqttClient.publish(mqttTopic, decimalValue, { qos: 1 }, function(err) {
                    if (err) {
                        updateMqttStatus('Gagal mengirim: ' + err.message, true);
                    } else {
                        updateMqttStatus(`Terkirim: ${currentChar}`, false);
                    }
                });
            } catch (e) {
                updateMqttStatus('Kesalahan konversi: ' + e.message, true);
            }
        }
    } else {
        console.log('No data available');
        document.getElementById('braille-dots').innerHTML = '';
        document.getElementById('braille-character').textContent = '';
        document.getElementById('page-info').textContent = 'Tidak ada data tersedia';
        document.getElementById('original-text').innerHTML = '';
        updateBrailleUnicodePattern(' ');
    }
}

// Text to speech
function speakCharacter() {
    const text = document.getElementById('braille-character').textContent;
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'id-ID';
    utterance.rate = 0.9;
    
    const voices = window.speechSynthesis.getVoices();
    const indoVoice = voices.find(v => v.lang === 'id-ID');
    if (indoVoice) utterance.voice = indoVoice;
    
    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(utterance);
    
    document.getElementById('announcements').textContent = 'Membaca: ' + text;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Button controls
    document.getElementById('btn-prev').onclick = function() {
        if (currentLineText) {
            const characters = currentLineText.split('');
            currentIndex = Math.max(0, currentIndex - 1);
        }
        updateView();
    };
    
    document.getElementById('btn-next').onclick = function() {
        if (currentLineText) {
            const characters = currentLineText.split('');
            currentIndex = Math.min(characters.length - 1, currentIndex + 1);
        }
        updateView();
    };
    
    document.getElementById('btn-read').onclick = speakCharacter;
    
    // Line navigation
    document.getElementById('btn-line-prev').onclick = function() {
        if (brailleData.lines && currentLineIndex > 0) {
            currentLineIndex--;
            currentLineText = brailleData.lines[currentLineIndex] || '';
            currentIndex = 0; // Reset character index when changing line
            updateView();
        }
    };
    
    document.getElementById('btn-line-next').onclick = function() {
        if (brailleData.lines && currentLineIndex < totalLines - 1) {
            currentLineIndex++;
            currentLineText = brailleData.lines[currentLineIndex] || '';
            currentIndex = 0; // Reset character index when changing line
            updateView();
        }
    };
    
    document.getElementById('btn-page-prev').onclick = function() {
        if (currentPage > 1) {
            fetchPageData(currentPage - 1);
        }
    };

    document.getElementById('btn-page-next').onclick = function() {
        if (currentPage < (brailleData.total_pages || 1)) {
            fetchPageData(currentPage + 1);
        }
    };
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                document.getElementById('btn-prev').click();
                break;
            case 'ArrowRight':
                e.preventDefault();
                document.getElementById('btn-next').click();
                break;
            case ' ':
                e.preventDefault();
                speakCharacter();
                break;
        }
    });
    
    // Load voices
    window.speechSynthesis.onvoiceschanged = function() {};
    
    // Initial render
    updateView();
    
    // Initialize braille unicode pattern
    if (currentLineText) {
        const firstChar = currentLineText.charAt(0);
        updateBrailleUnicodePattern(firstChar);
    }
    
    // Announce page load
    setTimeout(() => {
        document.getElementById('announcements').textContent = 
            'Halaman pembelajaran siap. Gunakan tombol atau keyboard untuk navigasi.';
    }, 1000);
});

// Handle complete session form submission
const completeSessionForm = document.getElementById('complete-session-form');
if (completeSessionForm) {
    completeSessionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        const formData = new FormData(this);
        
        // Disable button and show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyelesaikan...';
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            alert('Terjadi kesalahan saat menyelesaikan sesi. Silakan coba lagi.');
        });
    });
}

// Cleanup on page leave
window.addEventListener('beforeunload', function() {
    if (mqttClient && mqttClient.connected) {
        mqttClient.end();
    }
});
</script>
@endpush
@endsection