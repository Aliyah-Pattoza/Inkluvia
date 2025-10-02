{{-- resources/views/user/materi-tersimpan.blade.php --}}
@extends('layouts.user')

@section('title', 'Materi Tersimpan')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Skip to main content link -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded z-50">
        Lewati ke konten utama
    </a>

    <!-- Page Header -->
    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Materi Tersimpan
                </h1>
                <p class="text-lg text-gray-600">
                    Koleksi materi yang telah Anda simpan untuk akses cepat
                </p>
            </div>
            <a href="{{ route('user.perpustakaan') }}" 
               class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                Kembali ke Perpustakaan
            </a>
        </div>
    </header>

    <!-- Quick Stats -->
    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <i class="fas fa-bookmark text-2xl text-blue-600" aria-hidden="true"></i>
                <span class="text-lg font-semibold text-gray-800" aria-live="polite">
                    Total {{ $materials->total() }} materi tersimpan
                </span>
            </div>
        </div>
    </div>

    <!-- Search and Filters (jika ada materi) -->
    @if($materials->total() > 0)
    <section class="bg-white rounded-xl shadow-sm p-6 mb-6" role="search" aria-label="Filter Materi Tersimpan">
        <h2 class="text-xl font-semibold mb-4">Filter Materi</h2>
        <form method="GET" action="{{ route('user.materi-tersimpan') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Cari dalam materi tersimpan
                    </label>
                    <input type="text" 
                           id="search"
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Judul, deskripsi, atau penerbit..." 
                           class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori
                    </label>
                    <select id="kategori" name="kategori" 
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('kategori') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200">
                        <i class="fas fa-filter mr-2" aria-hidden="true"></i>
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </section>
    @endif

    <!-- Materials List -->
    <main id="main-content" role="main" aria-label="Daftar Materi Tersimpan">
        @if($materials->count() > 0)
        <div class="space-y-4 mb-6">
            <div role="status" aria-live="polite" class="text-sm text-gray-600 mb-4">
                Menampilkan {{ $materials->firstItem() }} sampai {{ $materials->lastItem() }} dari {{ $materials->total() }} materi tersimpan
            </div>

            @foreach($materials as $material)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border-2 border-gray-200 hover:border-primary focus-within:border-primary focus-within:ring-4 focus-within:ring-primary-light">
                <div class="p-6">
                    <header class="mb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ $material->judul }}
                                </h3>
                                
                                <!-- Meta Information -->
                                <div class="flex flex-wrap gap-3 mb-3">
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
                            
                            <!-- Remove from Saved -->
                            <button onclick="toggleSaved({{ $material->id }})"
                                    class="ml-4 p-3 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-300 transition-colors duration-200"
                                    aria-label="Hapus dari materi tersimpan"
                                    data-material-id="{{ $material->id }}">
                                <i class="fas fa-bookmark-slash text-2xl text-red-500" aria-hidden="true"></i>
                            </button>
                        </div>

                        @if($material->deskripsi)
                        <p class="text-gray-600 leading-relaxed">
                            {{ Str::limit($material->deskripsi, 200) }}
                        </p>
                        @endif
                    </header>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('user.perpustakaan.preview-page', $material) }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-eye mr-2" aria-hidden="true"></i>
                            Lihat Detail
                        </a>
                        
                        <a href="{{ route('user.perpustakaan.send', $material) }}"
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                            Kirim ke EduBraille
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($materials->hasPages())
        <nav aria-label="Navigasi halaman" class="bg-white rounded-xl shadow-sm p-4">
            {{ $materials->links('pagination::tailwind') }}
        </nav>
        @endif
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-bookmark text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">
                @if(request()->hasAny(['search', 'kategori', 'tingkat']))
                    Tidak Ada Materi yang Sesuai Filter
                @else
                    Belum Ada Materi Tersimpan
                @endif
            </h3>
            <p class="text-gray-500 mb-6">
                @if(request()->hasAny(['search', 'kategori', 'tingkat']))
                    Tidak ada materi tersimpan yang sesuai dengan filter yang Anda pilih.
                @else
                    Mulai jelajahi perpustakaan dan simpan materi yang menarik untuk Anda.
                @endif
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                @if(request()->hasAny(['search', 'kategori', 'tingkat']))
                <a href="{{ route('user.materi-tersimpan') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200">
                    <i class="fas fa-redo mr-2" aria-hidden="true"></i>
                    Reset Filter
                </a>
                @endif
                <a href="{{ route('user.perpustakaan') }}" 
                   class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200">
                    <i class="fas fa-book mr-2" aria-hidden="true"></i>
                    Jelajahi Perpustakaan
                </a>
            </div>
        </div>
        @endif
    </main>
</div>

<!-- Live Region for Announcements -->
<div id="announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>
@endsection

@push('scripts')
<script>
// Remove from saved materials
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
            // Remove the material card from view
            const materialCard = document.querySelector(`[data-material-id="${materialId}"]`).closest('article');
            materialCard.style.opacity = '0';
            materialCard.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                materialCard.remove();
                
                // Check if no materials left
                const remainingCards = document.querySelectorAll('article');
                if (remainingCards.length === 0) {
                    location.reload(); // Reload to show empty state
                }
            }, 300);
            
            announceToScreenReader(data.message);
        } else {
            announceToScreenReader(data.message || 'Gagal menghapus materi dari daftar tersimpan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        announceToScreenReader('Terjadi kesalahan saat menghapus materi');
    });
}

// Screen reader announcement
function announceToScreenReader(message) {
    const announcement = document.getElementById('announcements');
    announcement.textContent = message;
    setTimeout(() => announcement.textContent = '', 1000);
}
</script>
@endpush

@push('styles')
<style>
/* Same accessibility styles as perpustakaan.blade.php */
.focus\:ring-4:focus {
    outline: none;
    box-shadow: 0 0 0 4px rgba(81, 53, 135, 0.3);
}

@media (prefers-contrast: high) {
    .border-2 { border-width: 3px; }
    .text-gray-600 { color: #374151; }
}

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}

button, a, input, select { min-height: 44px; }

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
    z-index: 9999;
}
</style>
@endpush