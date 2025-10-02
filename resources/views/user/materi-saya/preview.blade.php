@extends('layouts.user')

@section('title', 'Preview Materi - ' . $material->judul)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded z-50">
        Lewati ke konten utama
    </a>

    <!-- Header -->
    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
                    {{ $material->judul }}
                </h1>
                
                <!-- Meta Information -->
                <div class="flex flex-wrap gap-2 mb-4">
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
                    @if($material->status === 'published')
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                        <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                        Terpublikasi
                    </span>
                    @endif
                </div>

                @if($material->deskripsi)
                <p class="text-gray-600 mb-2">{{ $material->deskripsi }}</p>
                @endif

                <div class="text-sm text-gray-500">
                    @if($material->penerbit)
                        Penerbit: {{ $material->penerbit }}
                    @endif
                    @if($material->tahun_terbit)
                        • Tahun: {{ $material->tahun_terbit }}
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                @if($material->created_by === Auth::id())
                <button onclick="toggleSaved({{ $material->id }})"
                        class="px-4 py-2 rounded-lg focus:outline-none focus:ring-4 transition-colors duration-200 {{ $isSaved ? 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300' }}"
                        id="save-button"
                        aria-label="{{ $isSaved ? 'Hapus dari tersimpan' : 'Simpan materi' }}">
                    <i class="fas fa-bookmark mr-2" aria-hidden="true"></i>
                    <span id="save-text">{{ $isSaved ? 'Tersimpan' : 'Simpan' }}</span>
                </button>
                @endif

                <a href="{{ route('user.materi-saya') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300">
                    <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                    Kembali
                </a>
            </div>
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
    <!-- View Toggle -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2" role="tablist" aria-label="Pilihan tampilan">
                <button onclick="switchView('text')" 
                        id="text-tab"
                        class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-primary bg-primary text-white"
                        role="tab"
                        aria-selected="true"
                        aria-controls="text-view">
                    <i class="fas fa-font mr-2" aria-hidden="true"></i>
                    Teks Normal
                </button>
                <button onclick="switchView('braille')" 
                        id="braille-tab"
                        class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-primary bg-gray-200 text-gray-700 hover:bg-gray-300"
                        role="tab"
                        aria-selected="false"
                        aria-controls="braille-view">
                    <i class="fas fa-braille mr-2" aria-hidden="true"></i>
                    Braille
                </button>
                <button onclick="switchView('split')" 
                        id="split-tab"
                        class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-primary bg-gray-200 text-gray-700 hover:bg-gray-300"
                        role="tab"
                        aria-selected="false"
                        aria-controls="split-view">
                    <i class="fas fa-columns mr-2" aria-hidden="true"></i>
                    Split View
                </button>
            </div>

            <div class="flex items-center gap-3">
                <!-- TTS Controls -->
                <button onclick="speakContent()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
                        aria-label="Baca dengan suara">
                    <i class="fas fa-volume-up mr-2" aria-hidden="true"></i>
                    Baca
                </button>
                <button onclick="stopSpeaking()" 
                        id="stop-button"
                        class="hidden px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300"
                        aria-label="Hentikan pembacaan">
                    <i class="fas fa-stop mr-2" aria-hidden="true"></i>
                    Stop
                </button>

                <!-- Send to Device -->
                @if($material->status === 'published')
                <a href="{{ route('user.perpustakaan.send', $material) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300">
                    <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                    Kirim
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <main id="main-content" role="main">
        <div class="bg-white rounded-xl shadow-sm">
            <!-- Navigation Controls -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button onclick="previousPage()" 
                                id="prev-btn"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Halaman sebelumnya">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <span id="page-info" class="text-sm font-medium text-gray-700" aria-live="polite">
                            Halaman <span id="current-page">1</span> dari <span id="total-pages">{{ count($jsonData['pages'] ?? []) }}</span>
                        </span>
                        <button onclick="nextPage()" 
                                id="next-btn"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Halaman selanjutnya">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>

                    <!-- Font Size Controls -->
                    <div class="flex items-center gap-2" role="group" aria-label="Kontrol ukuran teks">
                        <button onclick="decreaseFontSize()" 
                                class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-300"
                                aria-label="Perkecil teks">
                            <i class="fas fa-minus" aria-hidden="true"></i>
                        </button>
                        <span class="text-sm font-medium text-gray-700">Ukuran</span>
                        <button onclick="increaseFontSize()" 
                                class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-300"
                                aria-label="Perbesar teks">
                            <i class="fas fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Text View -->
            <div id="text-view" class="p-6" role="tabpanel" tabindex="0">
                <div id="text-content" class="prose max-w-none"></div>
            </div>

            <!-- Braille View -->
            <div id="braille-view" class="p-6 hidden" role="tabpanel" tabindex="0">
                <div id="braille-content" class="font-mono text-2xl leading-relaxed"></div>
            </div>

            <!-- Split View -->
            <div id="split-view" class="p-6 hidden" role="tabpanel">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="border-2 border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Teks Normal</h3>
                        <div id="split-text-content" class="prose max-w-none"></div>
                    </div>
                    <div class="border-2 border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Braille</h3>
                        <div id="split-braille-content" class="font-mono text-2xl leading-relaxed"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @endif
</div>

<!-- Live Region -->
<div id="announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>
@endsection

@push('scripts')
<script>
// Global variables
let jsonData = @json($jsonData ?? []);
let brailleData = @json($brailleData ?? null);
let currentPage = 1;
let totalPages = jsonData.pages ? jsonData.pages.length : 0;
let fontSize = 16;
let currentView = 'text';
let isSaved = {{ $isSaved ? 'true' : 'false' }};
let speechSynthesis = window.speechSynthesis;
let currentUtterance = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    if (totalPages > 0) {
        displayPage(1);
        updateNavigationButtons();
    }
    announceToScreenReader(`Preview materi ${jsonData.judul || 'dimuat'}. Total ${totalPages} halaman.`);
});

// Switch view
function switchView(view) {
    currentView = view;
    
    // Update tabs
    ['text-tab', 'braille-tab', 'split-tab'].forEach(id => {
        const tab = document.getElementById(id);
        if (id === view + '-tab') {
            tab.className = 'px-4 py-2 rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-primary bg-primary text-white';
            tab.setAttribute('aria-selected', 'true');
        } else {
            tab.className = 'px-4 py-2 rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-primary bg-gray-200 text-gray-700 hover:bg-gray-300';
            tab.setAttribute('aria-selected', 'false');
        }
    });
    
    // Show/hide views
    document.getElementById('text-view').classList.toggle('hidden', view !== 'text');
    document.getElementById('braille-view').classList.toggle('hidden', view !== 'braille');
    document.getElementById('split-view').classList.toggle('hidden', view !== 'split');
    
    // Display current page in new view
    displayPage(currentPage);
    
    announceToScreenReader(`Beralih ke tampilan ${view === 'text' ? 'teks normal' : view === 'braille' ? 'braille' : 'split view'}`);
}

// Display page
function displayPage(pageNumber) {
    if (pageNumber < 1 || pageNumber > totalPages) return;
    
    currentPage = pageNumber;
    document.getElementById('current-page').textContent = currentPage;
    
    const textPage = jsonData.pages[pageNumber - 1];
    const braillePage = brailleData?.pages ? brailleData.pages[pageNumber - 1] : null;
    
    // Text content
    let textHtml = `<h3 class="text-lg font-semibold mb-4 text-primary">Halaman ${pageNumber}</h3>`;
    if (textPage && textPage.lines) {
        textPage.lines.forEach(line => {
            if (line.text && line.text.trim()) {
                textHtml += `<p class="mb-3 leading-relaxed">${escapeHtml(line.text)}</p>`;
            }
        });
    }
    
    // Braille content
    let brailleHtml = `<h3 class="text-lg font-semibold mb-4 text-primary">Halaman ${pageNumber}</h3>`;
    if (braillePage && braillePage.lines) {
        braillePage.lines.forEach(line => {
            if (line.text && line.text.trim()) {
                brailleHtml += `<p class="mb-3 leading-relaxed">${escapeHtml(line.text)}</p>`;
            }
        });
    } else {
        brailleHtml += `<p class="text-gray-500 italic">Konversi Braille sedang diproses atau belum tersedia.</p>`;
    }
    
    // Update views
    document.getElementById('text-content').innerHTML = textHtml;
    document.getElementById('braille-content').innerHTML = brailleHtml;
    document.getElementById('split-text-content').innerHTML = textHtml;
    document.getElementById('split-braille-content').innerHTML = brailleHtml;
    
    updateNavigationButtons();
    announceToScreenReader(`Halaman ${pageNumber} dari ${totalPages} ditampilkan`);
}

// Navigation
function previousPage() {
    if (currentPage > 1) displayPage(currentPage - 1);
}

function nextPage() {
    if (currentPage < totalPages) displayPage(currentPage + 1);
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;
}

// Font size
function increaseFontSize() {
    if (fontSize < 32) {
        fontSize += 2;
        updateFontSize();
        announceToScreenReader(`Ukuran teks ${fontSize}px`);
    }
}

function decreaseFontSize() {
    if (fontSize > 12) {
        fontSize -= 2;
        updateFontSize();
        announceToScreenReader(`Ukuran teks ${fontSize}px`);
    }
}

function updateFontSize() {
    const elements = ['text-content', 'braille-content', 'split-text-content', 'split-braille-content'];
    elements.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.fontSize = fontSize + 'px';
    });
}

// Text-to-speech
function speakContent() {
    if (!speechSynthesis) {
        announceToScreenReader('Fitur text-to-speech tidak tersedia');
        return;
    }
    
    stopSpeaking();
    
    const page = jsonData.pages[currentPage - 1];
    if (!page || !page.lines) {
        announceToScreenReader('Tidak ada konten untuk dibaca');
        return;
    }
    
    let text = `Halaman ${currentPage}. `;
    page.lines.forEach(line => {
        if (line.text && line.text.trim()) {
            text += line.text + '. ';
        }
    });
    
    currentUtterance = new SpeechSynthesisUtterance(text);
    currentUtterance.lang = 'id-ID';
    currentUtterance.rate = 0.8;
    
    currentUtterance.onstart = function() {
        document.getElementById('stop-button').classList.remove('hidden');
        announceToScreenReader('Mulai membaca');
    };
    
    currentUtterance.onend = function() {
        document.getElementById('stop-button').classList.add('hidden');
        currentUtterance = null;
    };
    
    speechSynthesis.speak(currentUtterance);
}

function stopSpeaking() {
    if (speechSynthesis && currentUtterance) {
        speechSynthesis.cancel();
        document.getElementById('stop-button').classList.add('hidden');
        currentUtterance = null;
        announceToScreenReader('Pembacaan dihentikan');
    }
}

// Toggle saved
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
        }
    })
    .catch(error => {
        console.error('Error:', error);
        announceToScreenReader('Terjadi kesalahan');
    });
}

function updateSaveButton() {
    const button = document.getElementById('save-button');
    const text = document.getElementById('save-text');
    
    if (button && text) {
        if (isSaved) {
            button.className = 'px-4 py-2 rounded-lg focus:outline-none focus:ring-4 transition-colors duration-200 bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300';
            button.setAttribute('aria-label', 'Hapus dari tersimpan');
            text.textContent = 'Tersimpan';
        } else {
            button.className = 'px-4 py-2 rounded-lg focus:outline-none focus:ring-4 transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300';
            button.setAttribute('aria-label', 'Simpan materi');
            text.textContent = 'Simpan';
        }
    }
}

// Utility
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function announceToScreenReader(message) {
    const announcement = document.getElementById('announcements');
    announcement.textContent = message;
    setTimeout(() => announcement.textContent = '', 1000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    
    if (e.key === 'ArrowLeft') {
        e.preventDefault();
        previousPage();
    } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        nextPage();
    } else if (e.key === 'Escape') {
        stopSpeaking();
    } else if (e.ctrlKey) {
        if (e.key === '=' || e.key === '+') {
            e.preventDefault();
            increaseFontSize();
        } else if (e.key === '-') {
            e.preventDefault();
            decreaseFontSize();
        }
    }
});

// Cleanup
window.addEventListener('beforeunload', stopSpeaking);
</script>
@endpush

@push('styles')
<style>
.prose p {
    margin-bottom: 1rem;
    font-size: inherit;
}

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

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush