@extends('layouts.app')

@section('title', 'Preview Materi - ' . $material->judul)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-1">{{ $material->judul }}</h2>
                    <p class="text-white-50 mb-0">
                        <i class="fas fa-user me-1"></i>{{ $material->creator->nama_lengkap ?? 'Unknown' }} • 
                        <i class="fas fa-calendar me-1"></i>{{ $material->created_at->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('user.perpustakaan') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Material Info Card -->
            <div class="card mb-4 border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-white mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Materi</h6>
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-2"><strong class="text-white">Judul:</strong><br>
                                        <span class="text-white-50">{{ $material->judul }}</span>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2"><strong class="text-white">Penerbit:</strong><br>
                                        <span class="text-white-50">{{ $material->penerbit ?? 'Tidak ada' }}</span>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2"><strong class="text-white">Tahun:</strong><br>
                                        <span class="text-white-50">{{ $material->tahun_terbit ?? 'Tidak ada' }}</span>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2"><strong class="text-white">Edisi:</strong><br>
                                        <span class="text-white-50">{{ $material->edisi ?? 'Tidak ada' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-white mb-3"><i class="fas fa-tags me-2"></i>Kategori & Tingkat</h6>
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">{{ $material->kategori ?? 'Tidak ada' }}</span>
                                <span class="badge bg-success">{{ $material->tingkat ?? 'Tidak ada' }}</span>
                            </div>
                            @if($material->deskripsi)
                                <h6 class="text-white mb-2">Deskripsi:</h6>
                                <p class="text-white-50 small">{{ $material->deskripsi }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Preview -->
            <div class="card border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-0">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-eye me-2"></i>Preview Konten
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($error))
                        <!-- Error State -->
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="text-warning mb-2">Gagal Memuat Preview</h5>
                            <p class="text-white-50">{{ $error }}</p>
                        </div>
                    @elseif(isset($jsonData) && isset($jsonData['pages']))
                        <!-- Content Display -->
                        <div class="content-preview">
                            @foreach($jsonData['pages'] as $page)
                                <div class="page-content mb-4 p-3 rounded" style="background: rgba(255, 255, 255, 0.05);">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-white mb-0">
                                            <i class="fas fa-file-alt me-2"></i>Halaman {{ $page['page'] }}
                                        </h6>
                                        <span class="badge bg-info">{{ count($page['lines'] ?? []) }} baris</span>
                                    </div>
                                    
                                    @if(isset($page['lines']) && count($page['lines']) > 0)
                                        <div class="content-text" style="font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.6;">
                                            @foreach($page['lines'] as $line)
                                                <div class="content-line mb-1 text-white-50">
                                                    <span class="line-number me-3 text-muted" style="font-size: 12px;">{{ $line['line'] }}.</span>
                                                    <span class="line-text">{{ $line['text'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Halaman kosong</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- No Data State -->
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted mb-2">Tidak Ada Data</h5>
                            <p class="text-muted">Konten materi tidak tersedia untuk preview.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <button onclick="sendToDevice({{ $material->id }})" class="btn btn-primary me-2">
                    <i class="fas fa-paper-plane me-1"></i>Kirim ke Device
                </button>
                <a href="{{ route('user.materials.download', $material) }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-download me-1"></i>Download PDF
                </a>
                @if($material->braille_data_path)
                    <a href="{{ route('user.materials.download-braille', $material) }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i>Download Braille
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function sendToDevice(materialId) {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...';
    button.disabled = true;
    
    // Simulate sending to device (you can implement actual device communication here)
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check me-1"></i>Terikirim';
        button.classList.remove('btn-primary');
        button.classList.add('btn-success');
        
        // Show success message
        showAlert('Materi berhasil dikirim ke device!', 'success');
        
        // Reset button after 3 seconds
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
        }, 3000);
    }, 2000);
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<style>
.content-preview {
    max-height: 600px;
    overflow-y: auto;
}

.content-line {
    border-left: 2px solid transparent;
    padding-left: 10px;
    transition: all 0.2s ease;
}

.content-line:hover {
    border-left-color: #8B5CF6;
    background: rgba(139, 92, 246, 0.1);
    padding-left: 15px;
}

.line-number {
    min-width: 30px;
    display: inline-block;
}

.page-content {
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.page-content:hover {
    border-color: rgba(139, 92, 246, 0.3);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
}
</style>
@endsection
