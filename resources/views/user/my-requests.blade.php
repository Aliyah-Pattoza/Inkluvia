@extends('layouts.app')

@section('title', 'Request Saya')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Request Materi Saya</h1>
                <a href="{{ route('user.request-materi') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Request Baru
                </a>
            </div>

            <!-- Requests List -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Request</h6>
                </div>
                <div class="card-body">
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Judul Materi</th>
                                        <th>Kategori</th>
                                        <th>Tingkat</th>
                                        <th>Prioritas</th>
                                        <th>Status</th>
                                        <th>Tanggal Request</th>
                                        <th>Tanggal Update</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>
                                                <strong>{{ $request->judul_materi }}</strong>
                                                @if($request->deskripsi)
                                                    <br><small class="text-muted">{{ Str::limit($request->deskripsi, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ \App\Models\Material::getKategoriOptions()[$request->kategori] ?? $request->kategori }}</td>
                                            <td>{{ \App\Models\Material::getTingkatOptions()[$request->tingkat] ?? $request->tingkat }}</td>
                                            <td>
                                                <span class="badge bg-{{ $request->prioritas_badge_color }}">
                                                    {{ \App\Models\MaterialRequest::getPrioritasOptions()[$request->prioritas] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $request->status_badge_color }}">
                                                    {{ \App\Models\MaterialRequest::getStatusOptions()[$request->status] }}
                                                </span>
                                            </td>
                                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $request->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="showRequestDetails({{ $request->id }})" 
                                                        title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">Belum ada request</h5>
                            <p class="text-gray-400">Mulai dengan membuat request materi baru</p>
                            <a href="{{ route('user.request-materi') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Request Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-labelledby="requestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestDetailsModalLabel">Detail Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="requestDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showRequestDetails(requestId) {
    // For now, we'll show basic info. In a real implementation, 
    // you might want to fetch detailed info via AJAX
    const request = @json($requests->items());
    const selectedRequest = request.find(r => r.id === requestId);
    
    if (selectedRequest) {
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informasi Request:</h6>
                    <p><strong>Judul:</strong> ${selectedRequest.judul_materi}</p>
                    <p><strong>Kategori:</strong> ${selectedRequest.kategori}</p>
                    <p><strong>Tingkat:</strong> ${selectedRequest.tingkat}</p>
                    <p><strong>Prioritas:</strong> 
                        <span class="badge bg-${selectedRequest.prioritas_badge_color}">
                            ${selectedRequest.prioritas}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Status & Timeline:</h6>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-${selectedRequest.status_badge_color}">
                            ${selectedRequest.status}
                        </span>
                    </p>
                    <p><strong>Tanggal Request:</strong> ${new Date(selectedRequest.created_at).toLocaleDateString('id-ID')}</p>
                    <p><strong>Terakhir Update:</strong> ${new Date(selectedRequest.updated_at).toLocaleDateString('id-ID')}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Deskripsi:</h6>
                    <p>${selectedRequest.deskripsi}</p>
                </div>
            </div>
            ${selectedRequest.admin_notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Catatan Admin:</h6>
                    <div class="alert alert-info">
                        ${selectedRequest.admin_notes}
                    </div>
                </div>
            </div>
            ` : ''}
        `;
        
        document.getElementById('requestDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('requestDetailsModal')).show();
    }
}
</script>
@endsection
