{{-- resources/views/user/perpustakaan.blade.php --}}
@extends('layouts.user')

@section('title', 'Perpustakaan')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Skip to main content link for screen readers -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded z-50">
        Lewati ke konten utama
    </a>

    <!-- Page Header -->
    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2" id="page-title">
            Perpustakaan Materi Braille
        </h1>
        <p class="text-lg text-gray-600">
            Jelajahi dan akses koleksi materi pembelajaran dalam format Braille
        </p>
    </header>

    <!-- Quick Actions -->
    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-6" role="region" aria-label="Aksi Cepat">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('user.materi-tersimpan') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors duration-200">
                    <i class="fas fa-bookmark mr-2" aria-hidden="true"></i>
                    Lihat Materi Tersimpan
                </a>
                <span class="text-sm text-gray-700" aria-live="polite">
                    <strong id="saved-count">{{ count($userSavedMaterials) }}</strong> materi tersimpan
                </span>
            </div>
            @if($userDevices->count() > 0)
            <div class="text-sm text-green-700 bg-green-100 px-3 py-1 rounded-full">
                <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                {{ $userDevices->count() }} perangkat terhubung
            </div>
            @endif
        </div>
    </div>

    <!-- Search and Filters -->
    <section class="bg-white rounded-xl shadow-sm p-6 mb-6" role="search" aria-label="Pencarian dan Filter">
        <h2 class="text-xl font-semibold mb-4">Cari Materi</h2>
        <form method="GET" action="{{ route('user.perpustakaan') }}" class="space-y-4">
            <!-- Search Bar -->
            <div class="relative">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    Kata Kunci Pencarian
                </label>
                <input type="text" 
                       id="search"
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Masukkan judul, deskripsi, atau penerbit..." 
                       class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary text-lg"
                       aria-describedby="search-help">
                <i class="fas fa-search absolute left-4 top-12 text-gray-400" aria-hidden="true"></i>
                <div id="search-help" class="text-sm text-gray-500 mt-1">
                    Tekan Enter untuk mencari
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Kategori Filter -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori Materi
                    </label>
                    <select id="kategori" 
                            name="kategori" 
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary text-lg">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('kategori') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tingkat Filter -->
                <div>
                    <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-2">
                        Tingkat Pendidikan
                    </label>
                    <select id="tingkat" 
                            name="tingkat" 
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary text-lg">
                        <option value="">Semua Tingkat</option>
                        @foreach(\App\Models\Material::getTingkatOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('tingkat') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary text-lg font-medium transition-colors duration-200">
                        <i class="fas fa-search mr-2" aria-hidden="true"></i>
                        Cari Materi
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Materials List -->
    <main id="main-content" role="main" aria-label="Daftar Materi">
        @if($materials->count() > 0)
        <div class="space-y-4 mb-6">
            <h2 class="sr-only">Daftar Materi Tersedia</h2>
            <div role="status" aria-live="polite" aria-atomic="true" class="text-sm text-gray-600 mb-4">
                Menampilkan {{ $materials->firstItem() }} sampai {{ $materials->lastItem() }} dari {{ $materials->total() }} materi
            </div>

            @foreach($materials as $material)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border-2 border-gray-200 hover:border-primary focus-within:border-primary focus-within:ring-4 focus-within:ring-primary-light">
                <div class="p-6">
                    <!-- Material Header -->
                    <header class="mb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ $material->judul }}
                                </h3>
                                <!-- Meta Information -->
                                <div class="flex flex-wrap gap-3 mb-3" role="list" aria-label="Informasi Materi">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium" role="listitem">
                                        <i class="fas fa-layer-group mr-1" aria-hidden="true"></i>
                                        <span aria-label="Tingkat pendidikan">{{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? $material->tingkat }}</span>
                                    </span>
                                    @if($material->kategori)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium" role="listitem">
                                        <i class="fas fa-tag mr-1" aria-hidden="true"></i>
                                        <span aria-label="Kategori materi">{{ \App\Models\Material::getKategoriOptions()[$material->kategori] ?? $material->kategori }}</span>
                                    </span>
                                    @endif
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full font-medium" role="listitem">
                                        <i class="fas fa-file-alt mr-1" aria-hidden="true"></i>
                                        <span aria-label="Jumlah halaman">{{ $material->total_halaman }} Halaman</span>
                                    </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full font-medium" role="listitem">
                                        <i class="fas fa-{{ $material->akses === 'public' ? 'globe' : 'building' }} mr-1" aria-hidden="true"></i>
                                        <span aria-label="Tingkat akses">{{ $material->akses_display }}</span>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Save Toggle -->
                            <button onclick="toggleSaved({{ $material->id }})"
                                    class="ml-4 p-3 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors duration-200"
                                    aria-label="{{ in_array($material->id, $userSavedMaterials) ? 'Hapus dari materi tersimpan' : 'Simpan materi ini' }}"
                                    data-material-id="{{ $material->id }}">
                                <i class="fas fa-bookmark text-2xl {{ in_array($material->id, $userSavedMaterials) ? 'text-blue-600' : 'text-gray-300' }}"
                                   id="saved-icon-{{ $material->id }}"
                                   aria-hidden="true"></i>
                            </button>
                        </div>

                        <!-- Description -->
                        @if($material->deskripsi)
                        <div class="mt-3">
                            <h4 class="sr-only">Deskripsi Materi</h4>
                            <p class="text-gray-600 leading-relaxed">
                                {{ Str::limit($material->deskripsi, 200) }}
                            </p>
                        </div>
                        @endif

                        <!-- Publisher and Year -->
                        <div class="mt-3 text-sm text-gray-500">
                            @if($material->penerbit || $material->tahun_terbit)
                            <span>
                                @if($material->penerbit)
                                    Penerbit: {{ $material->penerbit }}
                                @endif
                                @if($material->penerbit && $material->tahun_terbit) • @endif
                                @if($material->tahun_terbit)
                                    Tahun: {{ $material->tahun_terbit }}
                                @endif
                            </span>
                            @endif
                        </div>
                    </header>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3" role="group" aria-label="Aksi untuk materi {{ $material->judul }}">
                        <!-- Preview Button -->
                        <a href="{{ route('user.perpustakaan.preview-page', $material) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 text-lg font-medium transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-eye mr-2" aria-hidden="true"></i>
                            Lihat Detail
                        </a>
                        
                        <!-- Send to Device Button -->
                        <a href="{{ route('user.perpustakaan.send', $material) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 text-lg font-medium transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                            Kirim ke EduBraille
                        </a>
                        <!-- Save Button -->
                        <button onclick="toggleSaved({{ $material->id }})"
                                class="px-4 py-2 rounded-lg focus:outline-none focus:ring-4 text-lg font-medium transition-colors duration-200 inline-flex items-center {{ in_array($material->id, $userSavedMaterials) ? 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300' }}"
                                aria-label="{{ in_array($material->id, $userSavedMaterials) ? 'Hapus dari daftar tersimpan' : 'Simpan materi ini' }}"
                                data-material-id="{{ $material->id }}">
                            <i class="fas fa-bookmark mr-2" aria-hidden="true"></i>
                            <span id="save-text-{{ $material->id }}">{{ in_array($material->id, $userSavedMaterials) ? 'Tersimpan' : 'Simpan' }}</span>
                        </button>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <nav aria-label="Navigasi halaman" class="bg-white rounded-xl shadow-sm p-4">
            {{ $materials->links('pagination::tailwind') }}
        </nav>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-book-open text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Materi Ditemukan</h3>
            <p class="text-gray-500 mb-6">
                @if(request('search') || request('kategori') || request('tingkat'))
                    Tidak ada materi yang sesuai dengan filter yang Anda pilih.
                @else
                    Belum ada materi yang tersedia di perpustakaan.
                @endif
            </p>
            @if(request()->hasAny(['search', 'kategori', 'tingkat']))
            <a href="{{ route('user.perpustakaan') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200">
                <i class="fas fa-redo mr-2" aria-hidden="true"></i>
                Reset Filter
            </a>
            @endif
        </div>
        @endif
    </main>
</div>

<!-- Live Region for Screen Reader Announcements -->
<div id="announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>
@endsection

@push('scripts')
<script>
let savedCount = {{ count($userSavedMaterials) }};

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
            const button = document.querySelector(`[data-material-id="${materialId}"]`);
            const text = document.getElementById(`save-text-${materialId}`);
            
            if (data.is_saved) {
                button.className = 'px-4 py-2 rounded-lg focus:outline-none focus:ring-4 text-lg font-medium transition-colors duration-200 inline-flex items-center bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300';
                button.setAttribute('aria-label', 'Hapus dari daftar tersimpan');
                text.textContent = 'Tersimpan';
                savedCount++;
            } else {
                button.className = 'px-4 py-2 rounded-lg focus:outline-none focus:ring-4 text-lg font-medium transition-colors duration-200 inline-flex items-center bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300';
                button.setAttribute('aria-label', 'Simpan materi ini');
                text.textContent = 'Simpan';
                savedCount--;
            }
            
            // Update saved count
            document.getElementById('saved-count').textContent = savedCount;
            
            // Announce to screen reader
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

// Announce to screen reader
function announceToScreenReader(message) {
    const announcement = document.getElementById('announcements');
    announcement.textContent = message;
    
    // Clear after announcement is read
    setTimeout(() => {
        announcement.textContent = '';
    }, 1000);
}

// Keyboard navigation enhancements
document.addEventListener('keydown', function(e) {
    // Add keyboard shortcuts for accessibility
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('search').focus();
        announceToScreenReader('Fokus pada kolom pencarian');
    }
});

// Initialize accessibility features
document.addEventListener('DOMContentLoaded', function() {
    // Add keyboard support for card navigation
    const materialCards = document.querySelectorAll('article');
    materialCards.forEach((card, index) => {
        // Add tabindex for keyboard navigation
        card.setAttribute('tabindex', '0');
        
        // Add keyboard event listeners
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const detailLink = card.querySelector('a[href*="preview-page"]');
                if (detailLink) {
                    detailLink.click();
                }
            }
        });
    });

    // Announce page load completion
    announceToScreenReader(`Halaman perpustakaan dimuat. Menampilkan ${materialCards.length} materi.`);
});
</script>
@endpush

@push('styles')
<style>
/* Enhanced accessibility styles */
.focus\:ring-4:focus {
    outline: none;
    box-shadow: 0 0 0 4px rgba(81, 53, 135, 0.3);
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .border-2 {
        border-width: 3px;
    }
    
    .text-gray-600 {
        color: #374151;
    }
    
    .bg-gray-100 {
        background-color: #f3f4f6;
    }
}

/* Reduced motion for accessibility */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Ensure interactive elements are large enough (44px minimum) */
button, a, input, select {
    min-height: 44px;
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

/* Focus styles for card navigation */
article:focus {
    outline: 3px solid #513587;
    outline-offset: 2px;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Improved button states */
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Skip link styles */
.skip-link {
    position: absolute;
    top: -40px;
    left: 6px;
    background: #513587;
    color: white;
    padding: 8px;
    border-radius: 4px;
    text-decoration: none;
    z-index: 1000;
}

.skip-link:focus {
    top: 6px;
}
</style>
@endpush