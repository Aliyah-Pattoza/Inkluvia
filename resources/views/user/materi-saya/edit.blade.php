@extends('layouts.user')

@section('title', 'Edit Materi')

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
                Edit Materi
            </h1>
            <a href="{{ route('user.materi-saya') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                Kembali
            </a>
        </div>
        <p class="text-gray-600">
            Edit informasi materi "{{ $material->judul }}"
        </p>
    </header>

    <!-- Form -->
    <main id="main-content" role="main">
        <form method="POST" action="{{ route('user.materi-saya.update', $material) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Materi <span class="text-red-500" aria-label="wajib diisi">*</span>
                    </label>
                    <input type="text" 
                           id="judul" 
                           name="judul" 
                           value="{{ old('judul', $material->judul) }}"
                           required
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('judul') border-red-500 @enderror"
                           aria-required="true">
                    @error('judul')
                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
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
                              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $material->deskripsi) }}</textarea>
                    @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
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
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kategori', $material->kategori) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tingkat -->
                    <div>
                        <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat Pendidikan <span class="text-red-500" aria-label="wajib diisi">*</span>
                        </label>
                        <select id="tingkat" 
                                name="tingkat" 
                                required
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary"
                                aria-required="true">
                            @foreach(\App\Models\Material::getTingkatOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('tingkat', $material->tingkat) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Penerbit & Tahun Terbit -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="penerbit" class="block text-sm font-medium text-gray-700 mb-2">
                            Penerbit
                        </label>
                        <input type="text" 
                               id="penerbit" 
                               name="penerbit" 
                               value="{{ old('penerbit', $material->penerbit) }}"
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="tahun_terbit" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Terbit
                        </label>
                        <input type="number" 
                               id="tahun_terbit" 
                               name="tahun_terbit" 
                               value="{{ old('tahun_terbit', $material->tahun_terbit) }}"
                               min="1900"
                               max="{{ date('Y') }}"
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
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
                           value="{{ old('edisi', $material->edisi) }}"
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                </div>

                <!-- File Upload (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ganti File PDF (Opsional)
                    </label>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                            File saat ini: <strong>{{ $material->judul }}.json</strong> ({{ $material->total_halaman }} halaman)
                        </p>
                        <p class="text-xs text-blue-600 mt-1">
                            Upload file baru hanya jika ingin mengganti konten. Proses konversi akan diulang.
                        </p>
                    </div>
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                    <span>Upload file baru</span>
                                    <input id="file" 
                                           name="file" 
                                           type="file" 
                                           accept=".pdf"
                                           class="sr-only"
                                           onchange="displayFileName(this)">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PDF hingga 40MB</p>
                            <p id="file-name" class="text-sm font-medium text-primary mt-2"></p>
                        </div>
                    </div>
                    @error('file')
                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            id="submit-btn"
                            class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200 font-medium">
                        <i class="fas fa-save mr-2" aria-hidden="true"></i>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('user.materi-saya') }}" 
                       class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors duration-200 font-medium">
                        <i class="fas fa-times mr-2" aria-hidden="true"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </main>
</div>

<!-- Processing Modal -->
<div id="processingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 text-center">
        <div class="mb-4">
            <i class="fas fa-sync fa-spin text-5xl text-primary" aria-hidden="true"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">
            Sedang Memproses...
        </h3>
        <p class="text-gray-600">
            Perubahan sedang disimpan. Jika Anda mengganti file, proses konversi akan memakan waktu beberapa menit.
        </p>
    </div>
</div>

<!-- Live Region -->
<div id="announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>
@endsection

@push('scripts')
<script>
function displayFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    
    if (fileName) {
        fileNameDisplay.textContent = `File baru dipilih: ${fileName}`;
        announceToScreenReader(`File ${fileName} telah dipilih. Konversi akan diulang.`);
    }
}

document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('file');
    
    if (fileInput.files.length > 0) {
        const modal = document.getElementById('processingModal');
        modal.classList.remove('hidden');
        document.getElementById('submit-btn').disabled = true;
        announceToScreenReader('Menyimpan perubahan dan memulai konversi ulang.');
    }
});

function announceToScreenReader(message) {
    const announcement = document.getElementById('announcements');
    announcement.textContent = message;
    setTimeout(() => announcement.textContent = '', 1000);
}