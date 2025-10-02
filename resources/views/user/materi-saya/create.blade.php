@extends('layouts.user')

@section('title', 'Upload Materi Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded z-50">
        Lewati ke konten utama
    </a>

    <!-- Page Header -->
    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-900">
                Upload Materi Baru
            </h1>
            <a href="{{ route('user.materi-saya') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                Kembali
            </a>
        </div>
        <p class="text-gray-600">
            Upload file PDF untuk dikonversi menjadi format Braille secara otomatis menggunakan AI
        </p>
    </header>

    <!-- Info Alert -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6" role="alert">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-400 text-xl mr-3 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <h3 class="text-sm font-medium text-blue-800 mb-1">Informasi Penting</h3>
                <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                    <li>File maksimal 40 MB</li>
                    <li>Format yang didukung: PDF</li>
                    <li>Konversi Braille menggunakan AI untuk hasil optimal</li>
                    <li>Proses konversi memerlukan waktu beberapa menit</li>
                    <li>Pastikan PDF dalam bahasa Indonesia untuk hasil terbaik</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form -->
    <main id="main-content" role="main">
        <form method="POST" action="{{ route('user.materi-saya.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Materi <span class="text-red-500" aria-label="wajib diisi">*</span>
                    </label>
                    <input type="text" 
                           id="judul" 
                           name="judul" 
                           value="{{ old('judul') }}"
                           required
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('judul') border-red-500 @enderror"
                           aria-required="true"
                           aria-describedby="judul-error">
                    @error('judul')
                    <p id="judul-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Materi
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4"
                              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('deskripsi') border-red-500 @enderror"
                              aria-describedby="deskripsi-help deskripsi-error">{{ old('deskripsi') }}</textarea>
                    <p id="deskripsi-help" class="mt-1 text-sm text-gray-500">
                        Jelaskan isi materi untuk memudahkan pencarian
                    </p>
                    @error('deskripsi')
                    <p id="deskripsi-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori & Tingkat -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kategori -->
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori
                        </label>
                        <select id="kategori" 
                                name="kategori" 
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('kategori') border-red-500 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kategori') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tingkat -->
                    <div>
                        <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat Pendidikan <span class="text-red-500" aria-label="wajib diisi">*</span>
                        </label>
                        <select id="tingkat" 
                                name="tingkat" 
                                required
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('tingkat') border-red-500 @enderror"
                                aria-required="true">
                            <option value="">Pilih Tingkat</option>
                            @foreach(\App\Models\Material::getTingkatOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('tingkat') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('tingkat')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Penerbit & Tahun Terbit -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Penerbit -->
                    <div>
                        <label for="penerbit" class="block text-sm font-medium text-gray-700 mb-2">
                            Penerbit
                        </label>
                        <input type="text" 
                               id="penerbit" 
                               name="penerbit" 
                               value="{{ old('penerbit') }}"
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('penerbit') border-red-500 @enderror">
                        @error('penerbit')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Terbit -->
                    <div>
                        <label for="tahun_terbit" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Terbit
                        </label>
                        <input type="number" 
                               id="tahun_terbit" 
                               name="tahun_terbit" 
                               value="{{ old('tahun_terbit') }}"
                               min="1900"
                               max="{{ date('Y') }}"
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('tahun_terbit') border-red-500 @enderror">
                        @error('tahun_terbit')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Edisi -->
                <div>
                    <label for="edisi" class="block text-sm font-medium text-gray-700 mb-2">
                        Edisi
                    </label>
                    <input type="text" 
                           id="edisi" 
                           name="edisi" 
                           value="{{ old('edisi') }}"
                           placeholder="Contoh: Edisi 1, Revisi 2024"
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('edisi') border-red-500 @enderror">
                    @error('edisi')
                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        File PDF <span class="text-red-500" aria-label="wajib diisi">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors @error('file') border-red-500 @enderror">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                    <span>Upload file</span>
                                    <input id="file" 
                                           name="file" 
                                           type="file" 
                                           accept=".pdf"
                                           required
                                           class="sr-only" 
                                           aria-required="true"
                                           aria-describedby="file-help"
                                           onchange="displayFileName(this)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p id="file-help" class="text-xs text-gray-500">
                                PDF hingga 40MB
                            </p>
                            <p id="file-name" class="text-sm font-medium text-primary mt-2"></p>
                        </div>
                    </div>
                    @error('file')
                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- AI Conversion Info -->
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 border-2 border-purple-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-robot text-2xl text-purple-600 mr-3 flex-shrink-0 mt-1" aria-hidden="true"></i>
                        <div>
                            <h3 class="text-sm font-semibold text-purple-900 mb-1">
                                Konversi Braille dengan AI
                            </h3>
                            <p class="text-sm text-purple-700 mb-2">
                                Materi Anda akan dikonversi menggunakan teknologi AI (ChatGPT) untuk hasil Braille yang akurat dan berkualitas tinggi.
                            </p>
                            <ul class="text-xs text-purple-600 space-y-1">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                    <span>Mendukung huruf, angka, operasi matematika, dan simbol</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                    <span>Konversi otomatis dengan standar Braille Indonesia</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                    <span>Hasil lebih bersih dan rapi dibanding konversi manual</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            id="submit-btn"
                            class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200 font-medium inline-flex items-center">
                        <i class="fas fa-upload mr-2" aria-hidden="true"></i>
                        Upload dan Konversi
                    </button>
                    <a href="{{ route('user.materi-saya') }}" 
                       class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200 font-medium inline-flex items-center">
                        <i class="fas fa-times mr-2" aria-hidden="true"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </main>
</div>

<!-- Processing Modal -->
<div id="processingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="processingTitle">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 text-center">
        <div class="mb-4">
            <i class="fas fa-sync fa-spin text-5xl text-primary" aria-hidden="true"></i>
        </div>
        <h3 id="processingTitle" class="text-xl font-bold text-gray-900 mb-2">
            Sedang Memproses...
        </h3>
        <p class="text-gray-600 mb-4">
            Materi sedang diupload dan dikonversi ke format Braille. Harap tunggu beberapa saat.
        </p>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-primary h-2.5 rounded-full animate-pulse" style="width: 70%"></div>
        </div>
        <p class="text-sm text-gray-500 mt-3">
            Jangan tutup halaman ini
        </p>
    </div>
</div>

<!-- Live Region -->
<div id="announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>
@endsection

@push('scripts')
<script>
// Display selected file name
function displayFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    
    if (fileName) {
        fileNameDisplay.textContent = `File dipilih: ${fileName}`;
        announceToScreenReader(`File ${fileName} telah dipilih`);
    } else {
        fileNameDisplay.textContent = '';
    }
}

// Form submission with loading indicator
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');
    const modal = document.getElementById('processingModal');
    
    // Show processing modal
    modal.classList.remove('hidden');
    submitBtn.disabled = true;
    
    announceToScreenReader('Upload materi dimulai. Harap tunggu.');
});

// Drag and drop file upload
const dropZone = document.querySelector('input[type="file"]').closest('div');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border-primary', 'bg-blue-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-primary', 'bg-blue-50');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    const fileInput = document.getElementById('file');
    
    if (files.length > 0) {
        fileInput.files = files;
        displayFileName(fileInput);
    }
}

// Screen reader announcements
function announceToScreenReader(message) {
    const announcement = document.getElementById('announcements');
    announcement.textContent = message;
    setTimeout(() => announcement.textContent = '', 1000);
}

// Validate file size before upload
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 40 * 1024 * 1024; // 40MB in bytes
    
    if (file && file.size > maxSize) {
        alert('Ukuran file terlalu besar! Maksimal 40MB.');
        e.target.value = '';
        document.getElementById('file-name').textContent = '';
        announceToScreenReader('File terlalu besar. Pilih file yang lebih kecil.');
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