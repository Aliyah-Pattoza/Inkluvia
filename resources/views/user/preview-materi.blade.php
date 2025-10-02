{{-- resources/views/user/preview-materi.blade.php --}}
@extends('layouts.user')

@section('title', 'Preview Materi - ' . $material->judul)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded z-50">
        Lewati ke konten utama
    </a>

    <!-- Header -->
    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                    {{ $material->judul }}
                </h1>
                
                <!-- Material metadata -->
                <div class="flex flex-wrap gap-3 mb-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">
                        <i class="fas fa-layer-group mr-1" aria-hidden="true"></i>
                        {{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? $material->tingkat }}
                    </span>
                    @if($material->kategori)
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                        <i class="fas fa-tag mr-1" aria-hidden="true"></i>
                        {{ \App\Models\Material::getKategoriOptions()[$material->kategori] ?? $material->kategori }}
                    </span>
                    @endif
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full font-medium">
                        <i class="fas fa-file-alt mr-1" aria-hidden="true"></i>
                        {{ $material->total_halaman }} Halaman
                    </span>
                </div>
            </div>

            <!-- Save Button -->
            <button onclick="toggleSaved({{ $material->id }})"
                    class="ml-4 px-4 py-2 rounded-lg focus:outline-none focus:ring-4 transition-colors duration-200 {{ $isSaved ? 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300' }}"
                    id="save-button"
                    aria-label="{{ $isSaved ? 'Hapus dari materi tersimpan' : 'Simpan materi ini' }}">
                <i class="fas fa-bookmark mr-2" aria-hidden="true"></i>
                <span id="save-text">{{ $isSaved ? 'Tersimpan' : 'Simpan' }}</span>
            </button>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('user.perpustakaan') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                Kembali ke Perpustakaan
            </a>
            
            <a href="{{ route('user.perpustakaan.send', $material) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition-colors duration-200">
                <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                Kirim ke EduBraille
            </a>

            <button onclick="speakMaterial()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors duration-200">
                <i class="fas fa-volume-up mr-2" aria-hidden="true"></i>
                Baca dengan Suara
            </button>

            <button onclick="stopSpeaking()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 transition-colors duration-200"
                    id="stop-button"
                    style="display: none;">
                <i class="fas fa-stop mr-2" aria-hidden="true"></i>
                Hentikan
            </button>
        </div>
    </header>

    @if(isset($error))
    <!-- Error State -->
    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 text-center">
        <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4" aria-hidden="true"></i>
        <h2 class="text-xl font-semibold text-red-800 mb-2">Konten Tidak Tersedia</h2>
        <p class="text-red-700">{{ $error }}</p>
    </div>
    @else
    <!-- Material Info -->
    <section class="bg-white rounded-xl shadow-sm p-6 mb-6" aria-label="Informasi Materi">
        <h2 class="text-xl font-semibold mb-4">Informasi Materi</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($material->penerbit)
            <div>
                <dt class="font-semibold text-gray-700">Penerbit:</dt>
                <dd class="text-gray-600">{{ $material->penerbit }}</dd>
            </div>
            @endif
            
            @if($material->tahun_terbit)
            <div>
                <dt class="font-semibold text-gray-700">Tahun Terbit:</dt>
                <dd class="text-gray-600">{{ $material->tahun_terbit }}</dd>
            </div>
            @endif
            
            @if($material->edisi)
            <div>
                <dt class="font-semibold text-gray-700">Edisi:</dt>
                <dd class="text-gray-600">{{ $material->edisi }}</dd>
            </div>
            @endif
            
            <div>
                <dt class="font-semibold text-gray-700">Tingkat Akses:</dt>
                <dd class="text-gray-600">{{ $material->akses_display }}</dd>
            </div>
        </dl>
        
        @if($material->deskripsi)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <h3 class="font-semibold text-gray-700 mb-2">Deskripsi:</h3>
            <p class="text-gray-600 leading-relaxed">{{ $material->deskripsi }}</p>
        </div>
        @endif
    </section>

    <!-- Material Content -->
    <main id="main-content" role="main" aria-label="Konten Materi">
        <div class="bg-white rounded-xl shadow-sm">
            <!-- Content Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Konten Materi</h2>
                    <div class="flex items-center space-x-4">
                        <!-- Page Navigation -->
                        <div class="flex items-center space-x-2">
                            <button onclick="previousPage()" 
                                    id="prev-btn"
                                    class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray
                                    disabled
                                    aria-label="Halaman sebelumnya">
                                <i class="fas fa-chevron-left" aria-hidden="true"></i>
                            </button>
                            <span id="page-info" class="text-sm text-gray-600" aria-live="polite">
                                Halaman <span id="current-page">1</span> dari <span id="total-pages">{{ count($jsonData['pages'] ?? []) }}</span>
                            </span>
                            <button onclick="nextPage()" 
                                    id="next-btn"
                                    class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
                                    aria-label="Halaman selanjutnya">
                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                            </button>
                        </div>
                        
                        <!-- Font Size Controls -->
                        <div class="flex items-center space-x-2" role="group" aria-label="Kontrol ukuran teks">
                            <button onclick="decreaseFontSize()" 
                                    class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
                                    aria-label="Perkecil ukuran teks">
                                <i class="fas fa-minus" aria-hidden="true"></i>
                            </button>
                            <span class="text-sm text-gray-600">A</span>
                            <button onclick="increaseFontSize()" 
                                    class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
                                    aria-label="Perbesar ukuran teks">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                @if(isset($jsonData) && isset($jsonData['pages']))
                <div id="content-area" class="prose max-w-none" tabindex="0" role="region" aria-label="Konten halaman">
                    <!-- Content will be populated by JavaScript -->
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Konten Tidak Tersedia</h3>
                    <p class="text-gray-500">Materi ini belum memiliki konten yang dapat ditampilkan.</p>
                </div>
                @endif
            </div>
        </div>
    </main>
    @endif
</div>

<!-- Live Region for Announcements -->
<div id="announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>
@endsection

@push('scripts')
<script>
// Global variables
let materialData = @json($jsonData ?? []);
let currentPage = 1;
let totalPages = materialData.pages ? materialData.pages.length : 0;
let fontSize = 16;
let isSaved = {{ $isSaved ? 'true' : 'false' }};
let speechSynthesis = window.speechSynthesis;
let currentUtterance = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (totalPages > 0) {
        displayPage(1);
        updateNavigationButtons();
    }
    
    // Announce page load
    announceToScreenReader(`Halaman preview materi ${materialData.judul || 'dimuat'}. Total ${totalPages} halaman.`);
});

// Display specific page content
function displayPage(pageNumber) {
    if (!materialData.pages || pageNumber < 1 || pageNumber > totalPages) {
        return;
    }
    
    const page = materialData.pages[pageNumber - 1];
    const contentArea = document.getElementById('content-area');
    
    if (!page || !page.lines) {
        contentArea.innerHTML = '<p class="text-gray-500 italic">Halaman ini kosong</p>';
        return;
    }
    
    // Build page content
    let html = `<div class="page-content" data-page="${pageNumber}">`;
    html += `<h3 class="text-lg font-semibold mb-4 text-primary">Halaman ${pageNumber}</h3>`;
    
    page.lines.forEach((line, index) => {
        if (line.text && line.text.trim()) {
            html += `<p class="mb-3 leading-relaxed" data-line="${index + 1}">${escapeHtml(line.text)}</p>`;
        }
    });
    
    html += '</div>';
    contentArea.innerHTML = html;
    
    // Update current page
    currentPage = pageNumber;
    document.getElementById('current-page').textContent = currentPage;
    
    // Update navigation buttons
    updateNavigationButtons();
    
    // Focus on content area for screen readers
    contentArea.focus();
    
    // Announce page change
    announceToScreenReader(`Halaman ${pageNumber} dari ${totalPages} ditampilkan`);
}

// Update navigation button states
function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;
    
    // Update ARIA labels
    prevBtn.setAttribute('aria-label', 
        currentPage <= 1 ? 'Halaman sebelumnya (tidak tersedia)' : 'Halaman sebelumnya'
    );
    nextBtn.setAttribute('aria-label', 
        currentPage >= totalPages ? 'Halaman selanjutnya (tidak tersedia)' : 'Halaman selanjutnya'
    );
}

// Navigation functions
function previousPage() {
    if (currentPage > 1) {
        displayPage(currentPage - 1);
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        displayPage(currentPage + 1);
    }
}

// Font size controls
function increaseFontSize() {
    if (fontSize < 24) {
        fontSize += 2;
        updateFontSize();
        announceToScreenReader(`Ukuran teks diperbesar menjadi ${fontSize} piksel`);
    }
}

function decreaseFontSize() {
    if (fontSize > 12) {
        fontSize -= 2;
        updateFontSize();
        announceToScreenReader(`Ukuran teks diperkecil menjadi ${fontSize} piksel`);
    }
}

function updateFontSize() {
    const contentArea = document.getElementById('content-area');
    contentArea.style.fontSize = fontSize + 'px';
}

// Toggle saved status
function toggleSaved(materialId) {
    fetch(`/user/perpustakaan/${materialId}/toggle-saved`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            isSaved = data.is_saved;
            updateSaveButton();
            announceToScreenReader(data.message);
        } else {
            announceToScreenReader(data.message || 'Gagal mengubah status penyimpanan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        announceToScreenReader('Terjadi kesalahan saat menyimpan materi');
    });
}

// Update save button appearance
function updateSaveButton() {
    const button = document.getElementById('save-button');
    const text = document.getElementById('save-text');
    
    if (isSaved) {
        button.className = 'ml-4 px-4 py-2 rounded-lg focus:outline-none focus:ring-4 transition-colors duration-200 bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300';
        button.setAttribute('aria-label', 'Hapus dari materi tersimpan');
        text.textContent = 'Tersimpan';
    } else {
        button.className = 'ml-4 px-4 py-2 rounded-lg focus:outline-none focus:ring-4 transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300';
        button.setAttribute('aria-label', 'Simpan materi ini');
        text.textContent = 'Simpan';
    }
}

// Text-to-speech functions
function speakMaterial() {
    if (!speechSynthesis) {
        announceToScreenReader('Fitur text-to-speech tidak tersedia di browser ini');
        return;
    }
    
    // Stop any ongoing speech
    stopSpeaking();
    
    // Get current page content
    const page = materialData.pages[currentPage - 1];
    if (!page || !page.lines) {
        announceToScreenReader('Tidak ada konten untuk dibaca pada halaman ini');
        return;
    }
    
    // Build text to speak
    let textToSpeak = `Halaman ${currentPage}. `;
    page.lines.forEach(line => {
        if (line.text && line.text.trim()) {
            textToSpeak += line.text + '. ';
        }
    });
    
    // Create and configure utterance
    currentUtterance = new SpeechSynthesisUtterance(textToSpeak);
    currentUtterance.lang = 'id-ID';
    currentUtterance.rate = 0.8;
    currentUtterance.pitch = 1.0;
    
    // Event handlers
    currentUtterance.onstart = function() {
        document.getElementById('stop-button').style.display = 'inline-block';
        announceToScreenReader('Mulai membaca halaman');
    };
    
    currentUtterance.onend = function() {
        document.getElementById('stop-button').style.display = 'none';
        currentUtterance = null;
    };
    
    currentUtterance.onerror = function(event) {
        console.error('Speech synthesis error:', event);
        document.getElementById('stop-button').style.display = 'none';
        announceToScreenReader('Terjadi kesalahan saat membaca teks');
        currentUtterance = null;
    };
    
    // Start speaking
    speechSynthesis.speak(currentUtterance);
}

function stopSpeaking() {
    if (speechSynthesis && currentUtterance) {
        speechSynthesis.cancel();
        document.getElementById('stop-button').style.display = 'none';
        currentUtterance = null;
        announceToScreenReader('Pembacaan dihentikan');
    }
}

// Screen reader announcements
function announceToScreenReader(message) {
    const announcement = document.getElementById('announcements');
    announcement.textContent = message;
    setTimeout(() => announcement.textContent = '', 1000);
}

// Utility function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Only handle shortcuts when not in input fields
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
        return;
    }
    
    switch(e.key) {
        case 'ArrowLeft':
            e.preventDefault();
            previousPage();
            break;
        case 'ArrowRight':
            e.preventDefault();
            nextPage();
            break;
        case 'Home':
            e.preventDefault();
            if (totalPages > 0) {
                displayPage(1);
            }
            break;
        case 'End':
            e.preventDefault();
            if (totalPages > 0) {
                displayPage(totalPages);
            }
            break;
        case 'Escape':
            e.preventDefault();
            stopSpeaking();
            break;
    }
    
    // Ctrl+shortcuts
    if (e.ctrlKey) {
        switch(e.key) {
            case '+':
            case '=':
                e.preventDefault();
                increaseFontSize();
                break;
            case '-':
                e.preventDefault();
                decreaseFontSize();
                break;
            case 's':
                e.preventDefault();
                toggleSaved({{ $material->id }});
                break;
        }
    }
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    stopSpeaking();
});
</script>
@endpush

@push('styles')
<style>
/* Enhanced accessibility and reading styles */
.prose {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.8;
    color: #374151;
}

.prose p {
    margin-bottom: 1rem;
    font-size: inherit;
}

.prose h3 {
    color: #513587;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Focus styles for content area */
#content-area:focus {
    outline: 2px solid #513587;
    outline-offset: 4px;
    border-radius: 8px;
}

/* Button states */
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

button:disabled:hover {
    background-color: inherit;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .prose {
        color: #000000;
    }
    
    .prose h3 {
        color: #000000;
        font-weight: 700;
    }
    
    button {
        border: 2px solid currentColor;
    }
}

/* Large text mode support */
@media (prefers-font-size: large) {
    .prose {
        font-size: 18px;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none;
    }
    
    .prose {
        font-size: 12pt;
        line-height: 1.6;
    }
}

/* Screen reader only content */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.sr-only:focus {
    position: fixed;
    top: 1rem;
    left: 1rem;
    width: auto;
    height: auto;
    padding: 0.75rem 1rem;
    margin: 0;
    overflow: visible;
    clip: auto;
    white-space: normal;
    background: #ffffff;
    border: 2px solid #513587;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 9999;
}

/* Ensure minimum touch target size */
button, a {
    min-height: 44px;
    min-width: 44px;
}

/* Focus visible for all interactive elements */
*:focus-visible {
    outline: 2px solid #513587;
    outline-offset: 2px;
}
</style>
@endpush