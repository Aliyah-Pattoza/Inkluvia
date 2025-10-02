@extends('layouts.admin')

@section('title', 'Manajemen Perangkat')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Manajemen Perangkat</h1>
            <p class="page-subtitle">Kelola perangkat EduBraille dalam sistem Inkluvia</p>
        </div>
        <a href="{{ route('admin.kelola-perangkat.create') }}" class="btn btn-primary">
            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
            Tambah Perangkat
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-laptop"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['total_devices'] }}</div>
            <div class="stat-label">Total Perangkat</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10B981, #059669);">
            <i class="fas fa-wifi"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['online_devices'] }}</div>
            <div class="stat-label">Online</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['active_devices'] }}</div>
            <div class="stat-label">Aktif</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
            <i class="fas fa-tools"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['maintenance_devices'] }}</div>
            <div class="stat-label">Maintenance</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" action="{{ route('admin.kelola-perangkat') }}">
        <div class="filters-grid">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search" class="form-label">Cari Perangkat</label>
                <input type="text" id="search" name="search" class="form-input" 
                       placeholder="Nama, serial, lembaga..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="status" class="form-label">Filter Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="connection" class="form-label">Filter Koneksi</label>
                <select id="connection" name="connection" class="form-select">
                    <option value="">Semua Koneksi</option>
                    <option value="online" {{ request('connection') === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('connection') === 'offline' ? 'selected' : '' }}>Offline</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="lembaga" class="form-label">Filter Lembaga</label>
                <select id="lembaga" name="lembaga" class="form-select">
                    <option value="">Semua Lembaga</option>
                    @foreach($lembagas as $lembaga)
                    <option value="{{ $lembaga->id }}" {{ request('lembaga') == $lembaga->id ? 'selected' : '' }}>
                        {{ $lembaga->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">&nbsp;</label>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.kelola-perangkat') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
    </form>
</div>

<!-- Devices Table -->
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Perangkat</th>
                <th>Serial Number</th>
                <th>Lembaga</th>
                <th>Pengguna</th>
                <th>Status</th>
                <th>Koneksi</th>
                <th>Terakhir Online</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devices as $device)
            <tr>
                <td>
                    <div style="display: flex; align-items: center;">
                        <div style="
                            width: 40px; 
                            height: 40px; 
                            border-radius: 8px; 
                            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
                            color: white; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center; 
                            font-weight: 600; 
                            margin-right: 0.75rem;
                        ">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $device->nama_device }}</div>
                            @if($device->keterangan)
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ Str::limit($device->keterangan, 30) }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <code style="background: var(--gray-100); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">
                        {{ $device->serial_number }}
                    </code>
                </td>
                <td>
                    <div>
                        <div style="font-weight: 600;">{{ $device->lembaga->nama ?? '-' }}</div>
                        @if($device->lembaga)
                        <div style="font-size: 0.75rem; color: var(--text-light);">{{ $device->lembaga->type }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    @if($device->user)
                    <div style="display: flex; align-items: center;">
                        <div class="user-avatar" style="width: 24px; height: 24px; font-size: 0.625rem; margin-right: 0.5rem;">
                            {{ strtoupper(substr($device->user->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 600;">{{ $device->user->nama_lengkap }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ $device->user->email }}</div>
                        </div>
                    </div>
                    @else
                    <span style="color: var(--text-light); font-style: italic;">Tidak ada</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $device->status_color }}">
                        {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                    </span>
                </td>
                <td>
                    @if($device->connection_status === 'online')
                    <span class="badge badge-success">
                        <i class="fas fa-wifi" style="margin-right: 0.25rem;"></i>
                        Online
                    </span>
                    @else
                    <span class="badge badge-danger">
                        <i class="fas fa-wifi" style="margin-right: 0.25rem; opacity: 0.5;"></i>
                        Offline
                    </span>
                    @endif
                </td>
                <td>
                    @if($device->last_connection)
                    <div style="font-size: 0.875rem;">{{ $device->last_connection->format('d/m/Y') }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-light);">{{ $device->last_connection->format('H:i') }}</div>
                    @else
                    <span style="color: var(--text-light); font-style: italic;">Belum pernah</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                        <button onclick="pingDevice('{{ $device->id }}')" class="btn btn-primary btn-sm" title="Ping Device">
                            <i class="fas fa-satellite-dish"></i>
                        </button>
                        <button onclick="requestStatus('{{ $device->id }}')" class="btn btn-secondary btn-sm" title="Request Status">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <a href="{{ route('admin.kelola-perangkat.edit', $device) }}" class="btn btn-secondary btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.kelola-perangkat.destroy', $device) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus perangkat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-light);">
                    <i class="fas fa-laptop" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; display: block;"></i>
                    Tidak ada perangkat ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($devices->hasPages())
<div class="pagination">
    {{ $devices->links() }}
</div>
@endif

<!-- Summary -->
<div style="margin-top: 1rem; text-align: center; color: var(--text-light); font-size: 0.875rem;">
    Menampilkan {{ $devices->firstItem() ?? 0 }} - {{ $devices->lastItem() ?? 0 }} dari {{ $devices->total() }} perangkat
</div>

<script>
function pingDevice(deviceId) {
    fetch(`/admin/manajemen-perangkat/${deviceId}/ping`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat mengirim ping');
    });
}

function requestStatus(deviceId) {
    fetch(`/admin/manajemen-perangkat/${deviceId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat meminta status');
    });
}
</script>
@endsection