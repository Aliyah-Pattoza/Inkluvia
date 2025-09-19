@extends('layouts.admin')

@section('title', 'Edit Materi Pembelajaran')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header with back button -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.manajemen-materi') }}" class="text-decoration-none me-3">
            <i class="fas fa-arrow-left text-primary" style="font-size: 1.2rem;"></i>
        </a>
        <h1 class="h4 mb-0 text-dark fw-bold">Edit Materi Pembelajaran</h1>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.manajemen-materi.update', $material) }}" method="POST" enctype="multipart/form-data" id="editForm">
                @csrf
                @method('PUT')
                
                <!-- Mandatory Fields Section -->
                <div class="mb-4">
                    <h5 class="text-dark fw-bold mb-3">Informasi Materi</h5>
                    
                    <!-- Judul Materi (Mandatory) -->
                    <div class="form-group mb-4">
                        <label for="judul" class="form-label fw-bold text-dark">Judul Materi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                               id="judul" name="judul" value="{{ old('judul', $material->judul) }}" 
                               placeholder="Masukkan judul materi pembelajaran" required
                               style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px;">
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tingkat Pembelajaran (Mandatory) -->
                    <div class="form-group mb-4">
                        <label for="tingkat" class="form-label fw-bold text-dark">Tingkat Pembelajaran <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <select class="form-control @error('tingkat') is-invalid @enderror" 
                                    id="tingkat" name="tingkat" required
                                    style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%238B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                <option value="">Pilih Tingkat</option>
                                @foreach(\App\Models\Material::getTingkatOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('tingkat', $material->tingkat) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tingkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Pengaturan Hak Akses (Mandatory) -->
                    <div class="form-group mb-4">
                        <label for="akses" class="form-label fw-bold text-dark">Pengaturan Hak Akses <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <select class="form-control @error('akses') is-invalid @enderror" 
                                    id="akses" name="akses" required
                                    style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%238B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                <option value="">Pilih Hak Akses</option>
                                @foreach(\App\Models\Material::getAksesOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('akses', $material->akses) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Optional Fields Section -->
                <div class="mb-4">
                    <h5 class="text-dark fw-bold mb-3">Informasi Tambahan (Opsional)</h5>
                    
                    <div class="row">
                        <!-- Tahun Terbit (Optional) -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="tahun_terbit" class="form-label fw-bold text-dark">Tahun Terbit</label>
                                <input type="number" class="form-control @error('tahun_terbit') is-invalid @enderror" 
                                       id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', $material->tahun_terbit) }}" 
                                       placeholder="Contoh: 2024" min="1900" max="{{ date('Y') }}"
                                       style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px;">
                                @error('tahun_terbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Penerbit (Optional) -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="penerbit" class="form-label fw-bold text-dark">Penerbit</label>
                                <input type="text" class="form-control @error('penerbit') is-invalid @enderror" 
                                       id="penerbit" name="penerbit" value="{{ old('penerbit', $material->penerbit) }}" 
                                       placeholder="Nama penerbit"
                                       style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px;">
                                @error('penerbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Edisi (Optional) -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="edisi" class="form-label fw-bold text-dark">Edisi</label>
                                <input type="text" class="form-control @error('edisi') is-invalid @enderror" 
                                       id="edisi" name="edisi" value="{{ old('edisi', $material->edisi) }}" 
                                       placeholder="Contoh: Edisi 1, Cetakan 2"
                                       style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px;">
                                @error('edisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kategori (Optional) -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="kategori" class="form-label fw-bold text-dark">Kategori</label>
                                <div class="position-relative">
                                    <select class="form-control @error('kategori') is-invalid @enderror" 
                                            id="kategori" name="kategori"
                                            style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%238B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                        <option value="">Pilih Kategori</option>
                                        @foreach(\App\Models\Material::getKategoriOptions() as $key => $value)
                                            <option value="{{ $key }}" {{ old('kategori', $material->kategori) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi (Optional) -->
                    <div class="form-group mb-4">
                        <label for="deskripsi" class="form-label fw-bold text-dark">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="4" 
                                  placeholder="Masukkan deskripsi materi pembelajaran"
                                  style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px; resize: vertical;">{{ old('deskripsi', $material->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status Section -->
                <div class="mb-4">
                    <h5 class="text-dark fw-bold mb-3">Pengaturan Status</h5>
                    
                    <div class="form-group mb-4">
                        <label for="status" class="form-label fw-bold text-dark">Status Materi <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required
                                    style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%238B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                <option value="">Pilih Status</option>
                                <option value="draft" {{ old('status', $material->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="processing" {{ old('status', $material->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="review" {{ old('status', $material->status) == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="published" {{ old('status', $material->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $material->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Upload Section (Disabled if file exists) -->
                <div class="mb-4">
                    <h5 class="text-dark fw-bold mb-3">File Materi</h5>
                    
                    <!-- Current File Info -->
                    @if($material->file_path)
                        <div class="alert alert-warning mb-3" style="border-radius: 8px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-lock text-warning me-2"></i>
                                <div>
                                    <strong>File tidak dapat diubah:</strong> {{ basename($material->file_path) }}
                                    <br>
                                    <small class="text-muted">File PDF sudah terupload dan tidak dapat diganti. Silakan hapus materi dan buat baru jika ingin mengubah file.</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- JSON Preview for existing material -->
                        <div class="mb-4">
                            <h6 class="text-dark fw-bold mb-3">
                                <i class="fas fa-code me-2"></i>JSON Preview
                            </h6>
                            <div class="alert alert-info" style="border-radius: 8px;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>Developer Info:</strong> This shows the current JSON format of the uploaded PDF for braille processing.
                                    </div>
                                </div>
                            </div>
                            <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                <pre id="jsonPreview" style="font-size: 12px; margin: 0; white-space: pre-wrap;">Loading JSON preview...</pre>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="loadJsonPreview()">
                                <i class="fas fa-refresh me-1"></i>Refresh Preview
                            </button>
                        </div>
                    @else
                        <!-- File Upload for new material -->
                        <div class="form-group">
                            <label for="file" class="form-label fw-bold text-dark">Upload File Baru</label>
                            <div class="upload-area @error('file') is-invalid @enderror" 
                                 id="uploadArea" 
                                 style="border: 2px dashed #8B5CF6; border-radius: 12px; padding: 40px; text-align: center; background-color: #f8f9ff; cursor: pointer; transition: all 0.3s ease;">
                                <div class="upload-content">
                                    <div class="upload-icon mb-3">
                                        <i class="fas fa-file-pdf text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="text-dark fw-medium mb-2">Drag & Drop file PDF atau klik untuk browse</p>
                                    <p class="text-muted small">Maksimal ukuran file: 10MB</p>
                                </div>
                                <input type="file" class="d-none" id="file" name="file" accept=".pdf">
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.manajemen-materi') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px;">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 8px; background-color: #8B5CF6; border-color: #8B5CF6;">
                        <i class="fas fa-save me-2"></i>Update Materi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.upload-area:hover {
    background-color: #f0f0ff !important;
    border-color: #7C3AED !important;
}

.upload-area.dragover {
    background-color: #e0e7ff !important;
    border-color: #7C3AED !important;
    transform: scale(1.02);
}

.form-control:focus {
    border-color: #8B5CF6 !important;
    box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25) !important;
}

.btn-primary:hover {
    background-color: #7C3AED !important;
    border-color: #7C3AED !important;
}

.alert-info {
    background-color: #e0f2fe;
    border-color: #8B5CF6;
    color: #1e40af;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('file');
    const uploadContent = uploadArea.querySelector('.upload-content');

    // Click to upload
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            updateFileDisplay(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            updateFileDisplay(file);
            validateFile(file);
        }
    });

    function updateFileDisplay(file) {
        uploadContent.innerHTML = `
            <div class="upload-icon mb-3">
                <i class="fas fa-file-pdf text-success" style="font-size: 3rem;"></i>
            </div>
            <p class="text-dark fw-medium mb-2">${file.name}</p>
            <p class="text-muted small">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearFile()">
                <i class="fas fa-times me-1"></i>Hapus File
            </button>
        `;
    }

    function validateFile(file) {
        // Check file type
        if (file.type !== 'application/pdf') {
            alert('Hanya file PDF yang diperbolehkan!');
            clearFile();
            return false;
        }
        
        // Check file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file maksimal 10MB!');
            clearFile();
            return false;
        }
        
        return true;
    }

    // Clear file function
    window.clearFile = function() {
        fileInput.value = '';
        uploadContent.innerHTML = `
            <div class="upload-icon mb-3">
                <i class="fas fa-file-pdf text-primary" style="font-size: 3rem;"></i>
            </div>
            <p class="text-dark fw-medium mb-2">Drag & Drop file PDF atau klik untuk browse</p>
            <p class="text-muted small">Maksimal ukuran file: 10MB</p>
        `;
    };

    // Load JSON preview function
    window.loadJsonPreview = function() {
        const jsonPreview = document.getElementById('jsonPreview');
        if (!jsonPreview) return;
        
        jsonPreview.textContent = 'Loading JSON preview...';
        
        // Fetch the actual JSON preview
        fetch(`/admin/manajemen-materi/{{ $material->id }}/preview`)
            .then(response => response.json())
            .then(data => {
                jsonPreview.textContent = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                console.error('Error loading JSON preview:', error);
                jsonPreview.textContent = 'Error loading JSON preview. Please try again.';
            });
    };

    // Load JSON preview on page load if file exists
    @if($material->file_path)
    document.addEventListener('DOMContentLoaded', function() {
        loadJsonPreview();
    });
    @endif

    // Form validation
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const requiredFields = ['judul', 'tingkat', 'akses', 'status'];
        let isValid = true;
        
        requiredFields.forEach(function(fieldName) {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
        }
    });
});
</script>
@endsection
