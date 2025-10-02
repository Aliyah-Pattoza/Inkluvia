@extends('layouts.admin')

@section('title', 'Content Manager - Perpustakaan')

@section('content')
<div class="container-fluid">
    <!-- Header with logo -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h1 class="h4 mb-0 text-dark fw-bold">Manajemen Materi</h1>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.manajemen-materi.create') }}" class="btn btn-primary me-3" style="border-radius: 8px;">
                <i class="fas fa-plus me-2"></i>Tambah Materi Baru
            </a>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.manajemen-materi') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <div class="position-relative">
                    <i class="fas fa-search position-absolute" style="top: 50%; left: 16px; transform: translateY(-50%); color: #6c757d;"></i>
                        <input type="text" class="form-control ps-8" name="search" 
                               value="{{ request('search') }}" placeholder="Cari materi"
                               style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px 12px 12px 40px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="fas fa-filter position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%); color: #8B5CF6; pointer-events: none;"></i>
                        <select name="kategori" class="form-control pe-4" 
                                style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px; appearance: none;">
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach(\App\Models\Material::getKategoriOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control" 
                            style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px;">
                        <option value="">Semua</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px;">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Materials Table -->
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            @if($materials->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0 py-3 px-4 fw-bold text-dark">Tanggal Publikasi</th>
                                <th class="border-0 py-3 px-4 fw-bold text-dark">Judul</th>
                                <th class="border-0 py-3 px-4 fw-bold text-dark">Tingkat</th>
                                <th class="border-0 py-3 px-4 fw-bold text-dark">Status</th>
                                <th class="border-0 py-3 px-4 fw-bold text-dark">Akses</th>
                                <th class="border-0 py-3 px-4 fw-bold text-dark">Braille</th>
                                <th class="border-0 py-3 px-4 fw-bold text-dark">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materials as $material)
                                <tr class="border-bottom">
                                    <td class="py-3 px-4">
                                        <span class="text-muted">{{ $material->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="fw-bold text-dark">{{ $material->judul }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-light text-dark border" style="border-radius: 6px;">
                                            {{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? ucfirst($material->tingkat) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="position-relative">
                                            <select class="form-select form-select-sm status-select" 
                                                    data-material-id="{{ $material->id }}" 
                                                    data-current-status="{{ $material->status }}"
                                                    style="font-size: 0.75rem; border-radius: 4px; min-width: 100px; padding: 4px 8px; appearance: none; padding-right: 24px;">
                                                <option value="draft" {{ $material->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="processing" {{ $material->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="review" {{ $material->status == 'review' ? 'selected' : '' }}>Review</option>
                                                <option value="published" {{ $material->status == 'published' ? 'selected' : '' }}>Published</option>
                                                <option value="archived" {{ $material->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                            </select>
                                            <i class="fas fa-chevron-down position-absolute" style="right: 8px; top: 50%; transform: translateY(-50%); color: #666; font-size: 10px; pointer-events: none;"></i>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-{{ $material->akses_badge_color }} text-white" style="border-radius: 6px;">
                                            {{ $material->akses_display }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($material->braille_data_path)
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="previewBraille({{ $material->id }})" 
                                                    title="Lihat Braille" style="border-radius: 6px;">
                                                <i class="fas fa-braille me-1"></i>Braille
                                            </button>
                                        @else
                                            <span class="text-muted small">Belum tersedia</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="d-flex gap-2">
                                            <!-- Read/Preview Button -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="previewMaterial({{ $material->id }})" 
                                                    title="Lihat Detail" style="border-radius: 6px;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.manajemen-materi.edit', $material) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit" style="border-radius: 6px;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.manajemen-materi.destroy', $material) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" style="border-radius: 6px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted">
                        Showing {{ $materials->firstItem() }} to {{ $materials->lastItem() }} of {{ $materials->total() }} results
                    </div>
                    <div class="pagination-container">
                        @if ($materials->hasPages())
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($materials->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $materials->previousPageUrl() }}" rel="prev">&laquo;</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($materials->getUrlRange(1, $materials->lastPage()) as $page => $url)
                                        @if ($page == $materials->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($materials->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $materials->nextPageUrl() }}" rel="next">&raquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">&raquo;</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted mb-2">Belum ada materi</h5>
                    <p class="text-muted mb-4">Mulai dengan menambahkan materi baru</p>
                    <a href="{{ route('admin.manajemen-materi.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                        <i class="fas fa-plus me-2"></i>Tambah Materi Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Material Preview Modal -->
<div class="modal fade" id="materialPreviewModal" tabindex="-1" aria-labelledby="materialPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background-color: #8B5CF6; color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold" id="materialPreviewModalLabel">
                    <i class="fas fa-book me-2"></i>Preview Materi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="previewContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat preview materi...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" id="downloadJsonBtn" style="border-radius: 8px;">
                    <i class="fas fa-download me-2"></i>Download JSON
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Braille Preview Modal -->
<div class="modal fade" id="braillePreviewModal" tabindex="-1" aria-labelledby="braillePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background-color: #17a2b8; color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold" id="braillePreviewModalLabel">
                    <i class="fas fa-braille me-2"></i>Preview Braille
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="brailleContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat konten braille...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-info" id="downloadBrailleBtn" style="border-radius: 8px;">
                    <i class="fas fa-download me-2"></i>Download Braille
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.table tbody tr:hover {
    background-color: #f8f9ff;
}

.btn-outline-primary:hover {
    background-color: #8B5CF6;
    border-color: #8B5CF6;
}

.btn-outline-warning:hover {
    background-color: #f59e0b;
    border-color: #f59e0b;
}

.btn-outline-danger:hover {
    background-color: #ef4444;
    border-color: #ef4444;
}

.modal-content {
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.json-content {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    max-height: 500px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.5;
}

.page-content {
    margin-bottom: 20px;
    padding: 15px;
    background-color: white;
    border-radius: 8px;
    border-left: 4px solid #8B5CF6;
}

.page-header {
    font-weight: bold;
    color: #8B5CF6;
    margin-bottom: 10px;
    font-size: 16px;
}

.line-content {
    margin: 5px 0;
    padding: 5px 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #8B5CF6;
}

.braille-content {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    max-height: 500px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.5;
}

.braille-page {
    margin-bottom: 20px;
    padding: 15px;
    background-color: white;
    border-radius: 8px;
    border-left: 4px solid #17a2b8;
}

.braille-page-header {
    font-weight: bold;
    color: #17a2b8;
    margin-bottom: 10px;
    font-size: 16px;
}

.braille-line {
    margin: 5px 0;
    padding: 5px 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #17a2b8;
    font-family: 'Courier New', monospace;
    font-size: 16px;
    letter-spacing: 1px;
}

.braille-text-display {
    font-family: 'Courier New', monospace;
    font-size: 18px;
    letter-spacing: 2px;
    background-color: #e3f2fd;
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #bbdefb;
    display: inline-block;
    margin: 2px;
}

/* Custom Pagination Styles */
.pagination {
    margin: 0;
    background: none;
    border: none;
}

.pagination .page-item {
    background: none;
    border: none;
    margin: 0 2px;
}

.pagination .page-link {
    color: #8B5CF6;
    background: white;
    border: 1px solid #e9ecef;
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: block;
    min-width: 40px;
    text-align: center;
}

.pagination .page-link:hover {
    background-color: #8B5CF6;
    color: white;
    border-color: #8B5CF6;
    text-decoration: none;
}

.pagination .page-item.active .page-link {
    background-color: #8B5CF6;
    border-color: #8B5CF6;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #e9ecef;
    cursor: not-allowed;
}

.pagination-container {
    display: flex;
    align-items: center;
}
</style>

<script>
let currentMaterialId = null;

// Debug function to test if everything is loaded
function debugInfo() {
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('Modal element:', document.getElementById('materialPreviewModal'));
    console.log('Current materials:', @json($materials->pluck('id')));
}

// Call debug function when page loads
document.addEventListener('DOMContentLoaded', function() {
    debugInfo();
});

function previewMaterial(materialId) {
    console.log('Preview material called with ID:', materialId);
    currentMaterialId = materialId;
    
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        alert('Bootstrap tidak tersedia. Silakan refresh halaman.');
        return;
    }
    
    const modalElement = document.getElementById('materialPreviewModal');
    if (!modalElement) {
        alert('Modal tidak ditemukan.');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    // Reset content
    document.getElementById('previewContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat preview materi...</p>
        </div>
    `;
    
    // Fetch material preview
    fetch(`/admin/manajemen-materi/${materialId}/preview`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            displayMaterialPreview(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('previewContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning mb-2">Gagal memuat preview</h5>
                    <p class="text-muted">${error.message || 'Terjadi kesalahan saat memuat data materi'}</p>
                </div>
            `;
        });
}

function displayMaterialPreview(data) {
    let html = '';
    
    if (data.pages && data.pages.length > 0) {
        html += '<div class="json-content">';
        
        // Material metadata (if available)
        if (data.judul || data.penerbit || data.tahun || data.edisi) {
            html += '<div class="mb-4 p-3 bg-primary text-white rounded">';
            html += '<h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Informasi Materi</h6>';
            html += `<p class="mb-1"><strong>Judul:</strong> ${data.judul || 'Tidak ada'}</p>`;
            html += `<p class="mb-1"><strong>Penerbit:</strong> ${data.penerbit || 'Tidak ada'}</p>`;
            html += `<p class="mb-1"><strong>Tahun:</strong> ${data.tahun || 'Tidak ada'}</p>`;
            html += `<p class="mb-0"><strong>Edisi:</strong> ${data.edisi || 'Tidak ada'}</p>`;
            html += '</div>';
        }
        
        // Raw JSON display
        html += '<div class="mb-4">';
        html += '<h6 class="mb-3"><i class="fas fa-code me-2"></i>JSON Data</h6>';
        html += '<pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto; font-size: 12px;">';
        html += JSON.stringify(data, null, 2);
        html += '</pre>';
        html += '</div>';
        html += '</div>';
    } else {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">Tidak ada konten</h5>
                <p class="text-muted">Materi ini belum memiliki konten yang dapat ditampilkan</p>
            </div>
        `;
    }
    
    document.getElementById('previewContent').innerHTML = html;
}

// Download JSON functionality
document.getElementById('downloadJsonBtn').addEventListener('click', function() {
    if (currentMaterialId) {
        window.open(`/admin/manajemen-materi/${currentMaterialId}/download-json`, '_blank');
    }
});

// Braille preview functionality
let currentBrailleMaterialId = null;

function previewBraille(materialId) {
    console.log('Preview braille called with ID:', materialId);
    currentBrailleMaterialId = materialId;
    
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        alert('Bootstrap tidak tersedia. Silakan refresh halaman.');
        return;
    }
    
    const modalElement = document.getElementById('braillePreviewModal');
    if (!modalElement) {
        alert('Modal braille tidak ditemukan.');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    // Reset content
    document.getElementById('brailleContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat konten braille...</p>
        </div>
    `;
    
    // Fetch braille content
    fetch(`/admin/manajemen-materi/${materialId}/braille-content`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            displayBrailleContent(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('brailleContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning mb-2">Gagal memuat braille</h5>
                    <p class="text-muted">${error.message || 'Terjadi kesalahan saat memuat konten braille'}</p>
                </div>
            `;
        });
}

function displayBrailleContent(data) {
    // Display pure JSON
    const jsonString = JSON.stringify(data, null, 2);
    const html = `<pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 12px;">${jsonString}</pre>`;
    document.getElementById('brailleContent').innerHTML = html;
}

// Download Braille functionality
document.getElementById('downloadBrailleBtn').addEventListener('click', function() {
    if (currentBrailleMaterialId) {
        window.open(`/admin/manajemen-materi/${currentBrailleMaterialId}/download-braille`, '_blank');
    }
});

// Status update functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const materialId = this.dataset.materialId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;
            
            if (newStatus === currentStatus) {
                return; // No change needed
            }
            
            // Show loading state
            const originalValue = this.value;
            this.disabled = true;
            this.style.opacity = '0.6';
            
            // Make API call to update status
            fetch(`/admin/manajemen-materi/${materialId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the dataset to reflect new current status
                    this.dataset.currentStatus = newStatus;
                    
                    // Show success message
                    showAlert('Status berhasil diperbarui!', 'success');
                } else {
                    // Revert to original value
                    this.value = currentStatus;
                    showAlert(data.error || 'Gagal memperbarui status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert to original value
                this.value = currentStatus;
                showAlert('Terjadi kesalahan saat memperbarui status', 'error');
            })
            .finally(() => {
                // Re-enable select
                this.disabled = false;
                this.style.opacity = '1';
            });
        });
    });
});


// Helper function to show alerts
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the content area
    const contentArea = document.querySelector('.content-area');
    contentArea.insertBefore(alertDiv, contentArea.firstChild);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection