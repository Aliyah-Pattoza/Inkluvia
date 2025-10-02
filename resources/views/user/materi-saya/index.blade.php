@extends('layouts.user')

@section('title', 'Materi Saya')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded z-50">
        Lewati ke konten utama
    </a>

    <!-- Page Header -->
    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Materi Saya
                </h1>
                <p class="text-lg text-gray-600">
                    Kelola materi yang Anda upload dan simpan
                </p>
            </div>
            <a href="{{ route('user.materi-saya.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200 text-lg font-medium"
               aria-label="Upload materi baru">
                <i class="fas fa-plus-circle mr-2" aria-hidden="true"></i>
                Upload Materi Baru
            </a>
        </div>
    </header>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Materi Diupload</p>
                    <p class="text-2xl font-bold text-blue-900" aria-live="polite">
                        {{ $materials->where('created_by', Auth::id())->count() }}
                    </p>
                </div>
                <i class="fas fa-upload text-3xl text-blue-600" aria-hidden="true"></i>
            </div>
        </div>

        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Materi Tersimpan</p>
                    <p class="text-2xl font-bold text-green-900" aria-live="polite">
                        {{ count($userSavedMaterials) }}
                    </p>
                </div>
                <i class="fas fa-bookmark text-3xl text-green-600" aria-hidden="true"></i>
            </div>
        </div>

        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium">Total Materi</p>
                    <p class="text-2xl font-bold text-purple-900" aria-live="polite">
                        {{ $materials->total() }}
                    </p>
                </div>
                <i class="fas fa-book text-3xl text-purple-600" aria-hidden="true"></i>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <section class="bg-white rounded-xl shadow-sm p-6 mb-6" role="search" aria-label="Pencarian dan Filter">
        <h2 class="text-xl font-semibold mb-4">Cari Materi</h2>
        <form method="GET" action="{{ route('user.materi-saya') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Search -->
                <div class="md:col-span-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Kata Kunci
                    </label>
                    <input type="text" 
                           id="search"
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari judul, penerbit..." 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                </div>

                <!-- Kategori -->
                <div class="md:col-span-2">
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori
                    </label>
                    <select id="kategori" name="kategori" 
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                        <option value="">Semua</option>
                        @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('kategori') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tingkat -->
                <div class="md:col-span-2">
                    <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-2">
                        Tingkat
                    </label>
                    <select id="tingkat" name="tingkat" 
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                        <option value="">Semua</option>
                        @foreach(\App\Models\Material::getTingkatOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('tingkat') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status" name="status" 
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                        <option value="">Semua</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Terpublikasi</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200">
                        <i class="fas fa-search mr-2" aria-hidden="true"></i>
                        Cari
                    </button>
                </div>
            </div>

            @if(request()->hasAny(['search', 'kategori', 'tingkat', 'status']))
            <div class="flex justify-end">
                <a href="{{ route('user.materi-saya') }}" 
                   class="text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary rounded px-3 py-1">
                    <i class="fas fa-redo mr-1" aria-hidden="true"></i>
                    Reset Filter
                </a>
            </div>
            @endif
        </form>
    </section>

    <!-- Materials List -->
    <main id="main-content" role="main" aria-label="Daftar Materi">
        @if($materials->count() > 0)
        <div class="space-y-4 mb-6">
            <div role="status" aria-live="polite" class="text-sm text-gray-600 mb-4">
                Menampilkan {{ $materials->firstItem() }} sampai {{ $materials->lastItem() }} dari {{ $materials->total() }} materi
            </div>

            @foreach($materials as $material)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border-2 border-gray-200 hover:border-primary focus-within:border-primary focus-within:ring-4 focus-within:ring-primary-light">
                <div class="p-6">
                    <header class="mb-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        {{ $material->judul }}
                                    </h3>
                                    
                                    <!-- Owner Badge -->
                                    @if($material->created_by === Auth::id())
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                                        <i class="fas fa-user mr-1" aria-hidden="true"></i>
                                        Milik Saya
                                    </span>
                                    @else
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                        <i class="fas fa-bookmark mr-1" aria-hidden="true"></i>
                                        Tersimpan
                                    </span>
                                    @endif
                                </div>
                                
                                <!-- Meta Information -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                                        <i class="fas fa-layer-group mr-1" aria-hidden="true"></i>
                                        {{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? $material->tingkat }}
                                    </span>
                                    @if($material->kategori)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                        <i class="fas fa-tag mr-1" aria-hidden="true"></i>
                                        {{ \App\Models\Material::getKategoriOptions()[$material->kategori] ?? $material->kategori }}
                                    </span>
                                    @endif
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                        <i class="fas fa-file-alt mr-1" aria-hidden="true"></i>
                                        {{ $material->total_halaman }} Halaman
                                    </span>
                                    
                                    <!-- Status Badge -->
                                    @if($material->status === 'published')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                        <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                                        Terpublikasi
                                    </span>
                                    @elseif($material->status === 'processing')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                        <i class="fas fa-sync fa-spin mr-1" aria-hidden="true"></i>
                                        Diproses
                                    </span>
                                    @elseif($material->status === 'draft')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                        <i class="fas fa-file mr-1" aria-hidden="true"></i>
                                        Draft
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($material->deskripsi)
                        <p class="text-gray-600 leading-relaxed mb-2">
                            {{ Str::limit($material->deskripsi, 200) }}
                        </p>
                        @endif

                        <p class="text-sm text-gray-500">
                            Terakhir diperbarui: {{ $material->updated_at->diffForHumans() }}
                        </p>
                    </header>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3" role="group" aria-label="Aksi untuk {{ $material->judul }}">
                        <!-- Preview Button -->
                        <a href="{{ route('user.materi-saya.preview', $material) }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-eye mr-2" aria-hidden="true"></i>
                            Preview
                        </a>

                        <!-- Send to Device (if published) -->
                        @if($material->status === 'published')
                        <a href="{{ route('user.perpustakaan.send', $material) }}"
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                            Kirim ke EduBraille
                        </a>
                        @endif

                        <!-- Edit Button (only for own materials) -->
                        @if($material->created_by === Auth::id())
                        <a href="{{ route('user.materi-saya.edit', $material) }}"
                           class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-4 focus:ring-yellow-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-edit mr-2" aria-hidden="true"></i>
                            Edit
                        </a>

                        <!-- Delete Button -->
                        <button onclick="confirmDelete({{ $material->id }})"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-trash mr-2" aria-hidden="true"></i>
                            Hapus
                        </button>
                        @else
                        <!-- Unsave Button (for saved materials) -->
                        <button onclick="toggleSaved({{ $material->id }})"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-bookmark-slash mr-2" aria-hidden="true"></i>
                            Hapus dari Tersimpan
                        </button>
                        @endif

                        <!-- Download JSON -->
                        <a href="{{ route('user.materi-saya.download', $material) }}"
                           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-download mr-2" aria-hidden="true"></i>
                            Download JSON
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
            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">
                @if(request()->hasAny(['search', 'kategori', 'tingkat', 'status']))
                    Tidak Ada Materi yang Sesuai Filter
                @else
                    Belum Ada Materi
                @endif
            </h3>
            <p class="text-gray-500 mb-6">
                @if(request()->hasAny(['search', 'kategori', 'tingkat', 'status']))
                    Tidak ada materi yang sesuai dengan filter yang Anda pilih.
                @else
                    Mulai upload materi Anda sendiri atau simpan materi dari perpustakaan.
                @endif
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                @if(request()->hasAny(['search', 'kategori', 'tingkat', 'status']))
                <a href="{{ route('user.materi-saya') }}" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200">
                    <i class="fas fa-redo mr-2" aria-hidden="true"></i>
                    Reset Filter
                </a>
                @endif
                <a href="{{ route('user.materi-saya.create') }}" 
                   class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200">
                    <i class="fas fa-plus-circle mr-2" aria-hidden="true"></i>
                    Upload Materi Baru
                </a>
                <a href="{{ route('user.perpustakaan') }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors duration-200">
                    <i class="fas fa-book mr-2" aria-hidden="true"></i>
                    Jelajahi Perpustakaan
                </a>
            </div>
        </div>
        @endif
    </main>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 id="deleteModalTitle" class="text-xl font-bold text-gray-900 mb-4">
            Konfirmasi Hapus
        </h3>
        <p class="text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus materi ini? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-300">
                Batal
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Live Region -->
<div id="announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>
@endsection

@push('scripts')
<script>
// Confirm delete
function confirmDelete(materialId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = `/user/materi-saya/${materialId}`;
    modal.classList.remove('hidden');
    announceToScreenReader('Dialog konfirmasi hapus dibuka');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    announceToScreenReader('Dialog konfirmasi hapus ditutup');
}

// Toggle saved status (for saved materials from library)
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
            announceToScreenReader(data.message);
            // Reload page to update list
            setTimeout(() => location.reload(), 500);
        } else {
            announceToScreenReader(data.message || 'Gagal mengubah status penyimpanan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        announceToScreenReader('Terjadi kesalahan');
    });
}

// Screen reader announcements
function announceToScreenReader(message) {
    const announcement = document.getElementById('announcements');
    announcement.textContent = message;
    setTimeout(() => announcement.textContent = '', 1000);
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Close modal on background click
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush

@push('styles')
<style>
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

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}

@media (prefers-contrast: high) {
    .border-2 { border-width: 3px; }
}
</style>
@endpush