@extends('layouts.user')

@section('title', 'Perpustakaan Digital')

@section('content')
<!-- Header -->
<div class="text-center mb-5">
    <h1 class="h2 mb-3 fw-bold" style="color: white;">Perpustakaan Digital</h1>
    <p style="color: rgba(255, 255, 255, 0.8);">Jelajahi koleksi materi pembelajaran yang tersedia</p>
</div>

    <!-- Search and Filters -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('user.perpustakaan') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="color: white;">Kategori</label>
                    <select name="kategori" class="form-control" 
                            style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px;">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\Material::getKategoriOptions() as $key => $value)
                            <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="color: white;">Tingkat</label>
                    <select name="tingkat" class="form-control" 
                            style="border: 1px solid #8B5CF6; border-radius: 8px; padding: 12px;">
                        <option value="">Semua Tingkat</option>
                        @foreach(\App\Models\Material::getTingkatOptions() as $key => $value)
                            <option value="{{ $key }}" {{ request('tingkat') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100" 
                            style="border-radius: 8px; background-color: #8B5CF6; border-color: #8B5CF6;">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Materials List -->
    @if($materials->count() > 0)
        <div class="materials-list">
            @foreach($materials as $material)
                <div class="material-card mb-4">
                    <div class="card h-100 shadow-sm border-0" 
                         style="border-radius: 12px; border-left: 4px solid #8B5CF6 !important;">
                        <div class="card-body p-4">
                            <!-- Material Title -->
                            <h5 class="card-title fw-bold mb-3" style="color: white;">
                                {{ $material->judul }}
                            </h5>
                            
                            <!-- Category and Level Badge -->
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border" 
                                      style="border-radius: 6px; font-size: 0.8rem;">
                                    {{ \App\Models\Material::getKategoriOptions()[$material->kategori] ?? ucfirst($material->kategori) }} - 
                                    {{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? ucfirst($material->tingkat) }}
                                </span>
                            </div>
                            
                            <!-- Description -->
                            <p class="card-text mb-4" style="font-size: 0.9rem; line-height: 1.5; color: rgba(255, 255, 255, 0.8);">
                                {{ $material->deskripsi ?: 'Tidak ada deskripsi' }}
                            </p>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-fill" 
                                        onclick="sendToDevice({{ $material->id }})"
                                        style="border-radius: 8px; background-color: #8B5CF6; border-color: #8B5CF6;">
                                    <i class="fas fa-paper-plane me-1"></i>Kirim ke device
                                </button>
                                <button class="btn btn-outline-primary btn-sm flex-fill" 
                                        onclick="previewMaterial({{ $material->id }})"
                                        style="border-radius: 8px; border-color: #8B5CF6; color: #8B5CF6;">
                                    <i class="fas fa-eye me-1"></i>Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $materials->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-book fa-3x mb-3" style="color: rgba(255, 255, 255, 0.3);"></i>
            <h5 style="color: rgba(255, 255, 255, 0.8);">Belum ada materi tersedia</h5>
            <p style="color: rgba(255, 255, 255, 0.6);">
                @if(request()->hasAny(['search', 'kategori', 'tingkat']))
                    Tidak ada materi yang sesuai dengan filter Anda.
                    <a href="{{ route('user.perpustakaan') }}" class="text-primary">Lihat semua materi</a>
                @else
                    Belum ada materi yang dipublikasikan.
                @endif
            </p>
        </div>
    @endif

<!-- Material Details Modal -->
<div class="modal fade" id="materialDetailsModal" tabindex="-1" aria-labelledby="materialDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialDetailsModalLabel">Detail Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="materialDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function previewMaterial(materialId) {
    // Open material preview in a new tab
    window.open(`/user/materials/${materialId}/preview`, '_blank');
}

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
@endsection
