@extends('layouts.admin')

@section('title', 'Manajemen Request Materi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Request Materi</h1>
        <div class="btn-group">
            <button class="btn btn-info" onclick="refreshStatistics()">
                <i class="fas fa-chart-bar"></i> Statistik
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" id="statisticsCards">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Approved Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="approvedCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rejectedCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.request-materi') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="Cari request...">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\MaterialRequest::getStatusOptions() as $key => $value)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="prioritas" class="form-control">
                        <option value="">Semua Prioritas</option>
                        @foreach(\App\Models\MaterialRequest::getPrioritasOptions() as $key => $value)
                            <option value="{{ $key }}" {{ request('prioritas') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card shadow mb-4">
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
                                <th>Peminta</th>
                                <th>Kategori</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Tanggal Request</th>
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
                                    <td>
                                        {{ $request->requester->nama_lengkap ?? $request->requester->name }}
                                        <br><small class="text-muted">{{ $request->requester->email }}</small>
                                    </td>
                                    <td>{{ \App\Models\Material::getKategoriOptions()[$request->kategori] ?? $request->kategori }}</td>
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
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.request-materi.show', $request) }}" 
                                               class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($request->status === 'pending')
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="approveRequest({{ $request->id }})" 
                                                        title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="rejectRequest({{ $request->id }})" 
                                                        title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @elseif($request->status === 'approved')
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="markInProgress({{ $request->id }})" 
                                                        title="Mark as In Progress">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @elseif($request->status === 'in_progress')
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="completeRequest({{ $request->id }})" 
                                                        title="Complete">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                        </div>
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
                    <p class="text-gray-400">Belum ada request materi dari pengguna</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Action Modals -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel">Konfirmasi Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="actionModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<script>
// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    refreshStatistics();
});

function refreshStatistics() {
    fetch('{{ route("admin.request-materi.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('pendingCount').textContent = data.pending_requests || 0;
            document.getElementById('approvedCount').textContent = data.approved_requests || 0;
            document.getElementById('completedCount').textContent = data.completed_requests || 0;
            document.getElementById('rejectedCount').textContent = data.rejected_requests || 0;
        })
        .catch(error => console.error('Error loading statistics:', error));
}

function approveRequest(requestId) {
    showActionModal(
        'Approve Request',
        '<p>Apakah Anda yakin ingin menyetujui request ini?</p><div class="form-group"><label for="admin_notes">Catatan Admin (Opsional):</label><textarea class="form-control" id="admin_notes" rows="3"></textarea></div>',
        () => {
            const notes = document.getElementById('admin_notes').value;
            submitRequestAction(requestId, 'approve', { admin_notes: notes });
        }
    );
}

function rejectRequest(requestId) {
    showActionModal(
        'Reject Request',
        '<p>Apakah Anda yakin ingin menolak request ini?</p><div class="form-group"><label for="admin_notes">Alasan Penolakan:</label><textarea class="form-control" id="admin_notes" rows="3" required></textarea></div>',
        () => {
            const notes = document.getElementById('admin_notes').value;
            if (!notes.trim()) {
                alert('Alasan penolakan harus diisi!');
                return;
            }
            submitRequestAction(requestId, 'reject', { admin_notes: notes });
        }
    );
}

function markInProgress(requestId) {
    showActionModal(
        'Mark as In Progress',
        '<p>Apakah Anda yakin ingin menandai request ini sebagai dalam proses?</p>',
        () => {
            submitRequestAction(requestId, 'in-progress', {});
        }
    );
}

function completeRequest(requestId) {
    // This would typically show a form to select the created material
    showActionModal(
        'Complete Request',
        '<p>Apakah Anda yakin ingin menyelesaikan request ini?</p><div class="form-group"><label for="material_id">Pilih Materi yang Dibuat:</label><select class="form-control" id="material_id" required><option value="">Pilih Materi</option></select></div>',
        () => {
            const materialId = document.getElementById('material_id').value;
            if (!materialId) {
                alert('Pilih materi yang dibuat!');
                return;
            }
            submitRequestAction(requestId, 'complete', { material_id: materialId });
        }
    );
}

function showActionModal(title, content, onConfirm) {
    document.getElementById('actionModalLabel').textContent = title;
    document.getElementById('actionModalBody').innerHTML = content;
    
    document.getElementById('confirmAction').onclick = onConfirm;
    
    new bootstrap.Modal(document.getElementById('actionModal')).show();
}

function submitRequestAction(requestId, action, data) {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }
    
    fetch(`/admin/request-materi/${requestId}/${action}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses request');
    });
}
</script>
@endsection
