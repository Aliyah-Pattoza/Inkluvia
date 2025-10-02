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
const brailleData = {!! $brailleData !!};

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
let currentPage = 1;

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

// Render braille dots
function renderBrailleDots(binary) {
    let html = '<div style="display: inline-block;">';
    for (let row = 0; row < 3; row++) {
        html += '<div class="braille-dot-row">';
        const leftIdx = row * 2;
        const rightIdx = row * 2 + 1;
        
        html += binary[leftIdx] === '1' 
            ? '<div class="braille-dot" role="img" aria-label="Dot filled"></div>'
            : '<div class="braille-dot-empty"></div>';
        
        html += binary[rightIdx] === '1'
            ? '<div class="braille-dot" role="img" aria-label="Dot filled"></div>'
            : '<div class="braille-dot-empty"></div>';
        
        html += '</div>';
    }
    html += '</div>';
    return html;
}

// Update display
function updateView() {
    const pageData = brailleData.filter(d => d.halaman == currentPage);
    if (!pageData.length) return;
    
    currentIndex = Math.max(0, Math.min(currentIndex, pageData.length - 1));
    
    const current = pageData[currentIndex];
    
    // Update braille dots
    document.getElementById('braille-dots').innerHTML = renderBrailleDots(current.braille);
    
    // Update character
    document.getElementById('braille-character').textContent = current.karakter;
    
    // Update page info
    document.getElementById('page-info').textContent = 
        `Halaman ${currentPage} • Karakter ${currentIndex + 1} dari ${pageData.length}`;
    
    // Update original text with highlighting
    document.getElementById('original-text').innerHTML = pageData.map((d, i) => {
        return i === currentIndex 
            ? `<span class="active-char">${d.karakter}</span>`
            : d.karakter;
    }).join(' ');
    
    // Send to MQTT
    if (mqttClient.connected) {
        try {
            const decimalValue = brailleToDecimal(current.braille);
            mqttClient.publish(mqttTopic, decimalValue, { qos: 1 }, function(err) {
                if (err) {
                    updateMqttStatus('Gagal mengirim: ' + err.message, true);
                } else {
                    updateMqttStatus(`Terkirim: ${current.karakter}`, false);
                }
            });
        } catch (e) {
            updateMqttStatus('Kesalahan konversi: ' + e.message, true);
        }
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
        currentIndex = Math.max(0, currentIndex - 1);
        updateView();
    };
    
    document.getElementById('btn-next').onclick = function() {
        const pageData = brailleData.filter(d => d.halaman == currentPage);
        currentIndex = Math.min(pageData.length - 1, currentIndex + 1);
        updateView();
    };
    
    document.getElementById('btn-read').onclick = speakCharacter;
    
    document.getElementById('btn-page-prev').onclick = function() {
        if (currentPage > 1) {
            currentPage--;
            currentIndex = 0;
            updateView();
        }
    };
    
    document.getElementById('btn-page-next').onclick = function() {
        const maxPage = Math.max(...brailleData.map(d => d.halaman));
        if (currentPage < maxPage) {
            currentPage++;
            currentIndex = 0;
            updateView();
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
    
    // Announce page load
    setTimeout(() => {
        document.getElementById('announcements').textContent = 
            'Halaman pembelajaran siap. Gunakan tombol atau keyboard untuk navigasi.';
    }, 1000);
});

// Cleanup on page leave
window.addEventListener('beforeunload', function() {
    if (mqttClient && mqttClient.connected) {
        mqttClient.end();
    }
});
</script>
@endpush
@endsection