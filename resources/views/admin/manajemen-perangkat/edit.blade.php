@extends('layouts.admin')

@section('title', 'Edit Perangkat')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('admin.kelola-perangkat') }}" class="btn btn-secondary" style="margin-right: 1rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div>
            <h1 class="page-title">Edit Perangkat</h1>
            <p class="page-subtitle">Edit data perangkat {{ $device->nama_device }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('admin.kelola-perangkat.update', $device) }}" id="deviceForm">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nama_device" class="form-label">Nama Perangkat <span style="color: var(--danger);">*</span></label>
            <input type="text" id="nama_device" name="nama_device" class="form-input" 
                   value="{{ old('nama_device', $device->nama_device) }}" required 
                   placeholder="Contoh: EduBraille Lab 1">
        </div>
        
        <div class="form-group">
            <label for="serial_number" class="form-label">Serial Number <span style="color: var(--danger);">*</span></label>
            <input type="text" id="serial_number" name="serial_number" class="form-input" 
                   value="{{ old('serial_number', $device->serial_number) }}" required
                   placeholder="Format: EDU + 6 digit hex">
            <small style="color: var(--text-light); font-size: 0.875rem;">
                Format: EDU + 6 digit hex (contoh: EDU1A2B3C)
            </small>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label for="lembaga_id" class="form-label">Lembaga <span style="color: var(--danger);">*</span></label>
                <select id="lembaga_id" name="lembaga_id" class="form-select" required onchange="handleLembagaChange()">
                    <option value="">Pilih Lembaga</option>
                    @foreach($lembagas as $lembaga)
                    <option value="{{ $lembaga->id }}" data-type="{{ $lembaga->type }}" 
                            {{ old('lembaga_id', $device->lembaga_id) == $lembaga->id ? 'selected' : '' }}>
                        {{ $lembaga->nama }} ({{ $lembaga->type }})
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="user_id" class="form-label">
                    Pengguna 
                    <span id="user_required" style="color: var(--danger); display: none;">*</span>
                </label>
                <select id="user_id" name="user_id" class="form-select">
                    <option value="">Pilih Pengguna</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $device->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->nama_lengkap }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>
                <small id="user_help" style="color: var(--text-light); font-size: 0.875rem; display: none;">
                    Wajib diisi untuk lembaga individu
                </small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="status" class="form-label">Status <span style="color: var(--danger);">*</span></label>
            <select id="status" name="status" class="form-select" required>
                <option value="">Pilih Status</option>
                <option value="aktif" {{ old('status', $device->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak_aktif" {{ old('status', $device->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                <option value="maintenance" {{ old('status', $device->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea id="keterangan" name="keterangan" class="form-input" rows="3" 
                      placeholder="Keterangan tambahan tentang perangkat">{{ old('keterangan', $device->keterangan) }}</textarea>
        </div>
        
        <!-- Device Info -->
        <div style="background: var(--gray-50); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <h4 style="margin-bottom: 0.5rem; color: var(--text-dark);">Informasi Perangkat</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.875rem; color: var(--text-light);">
                <div>
                    <strong>Dibuat:</strong> {{ $device->created_at->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong>Terakhir Update:</strong> {{ $device->updated_at->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong>Terakhir Online:</strong> 
                    {{ $device->last_connection ? $device->last_connection->format('d/m/Y H:i') : 'Belum pernah' }}
                </div>
                <div>
                    <strong>Status Koneksi:</strong> 
                    <span class="badge badge-{{ $device->connection_status === 'online' ? 'success' : 'danger' }}">
                        {{ ucfirst($device->connection_status) }}
                    </span>
                </div>
            </div>
            
            @if($device->device_info)
            <div style="margin-top: 1rem;">
                <strong>Device Info:</strong>
                <div style="background: white; padding: 0.75rem; border-radius: 6px; margin-top: 0.5rem;">
                    @foreach($device->device_info as $key => $value)
                    <div style="display: inline-block; margin-right: 1rem;">
                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Preview Card -->
        <div style="background: var(--gray-50); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid var(--border-color);">
            <h4 style="margin-bottom: 1rem; color: var(--text-dark); display: flex; align-items: center;">
                <i class="fas fa-eye" style="margin-right: 0.5rem;"></i>
                Preview Perangkat
            </h4>
            <div style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
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
                        <div style="font-weight: 600;" id="preview-nama">{{ $device->nama_device }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-light);">
                            <code id="preview-serial" style="background: var(--gray-100); padding: 0.125rem 0.25rem; border-radius: 4px;">{{ $device->serial_number }}</code>
                        </div>
                        <div style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.25rem;">
                            <span id="preview-lembaga">{{ $device->lembaga->nama ?? 'Lembaga' }}</span>
                            <span id="preview-user" style="margin-left: 0.5rem;">{{ $device->user ? '| ' . $device->user->nama_lengkap : '' }}</span>
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <span class="badge badge-{{ $device->status_color }}" id="preview-status">{{ ucfirst(str_replace('_', ' ', $device->status)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('admin.kelola-perangkat') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Perangkat
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const namaInput = document.getElementById('nama_device');
    const serialInput = document.getElementById('serial_number');
    const lembagaSelect = document.getElementById('lembaga_id');
    const userSelect = document.getElementById('user_id');
    const statusSelect = document.getElementById('status');
    const userRequired = document.getElementById('user_required');
    const userHelp = document.getElementById('user_help');
    
    // Preview elements
    const previewNama = document.getElementById('preview-nama');
    const previewSerial = document.getElementById('preview-serial');
    const previewLembaga = document.getElementById('preview-lembaga');
    const previewUser = document.getElementById('preview-user');
    const previewStatus = document.getElementById('preview-status');
    
    function updatePreview() {
        previewNama.textContent = namaInput.value || 'Nama Perangkat';
        previewSerial.textContent = serialInput.value || 'Serial Number';
        
        const selectedLembaga = lembagaSelect.options[lembagaSelect.selectedIndex];
        previewLembaga.textContent = selectedLembaga.text !== 'Pilih Lembaga' ? selectedLembaga.text : 'Lembaga';
        
        const selectedUser = userSelect.options[userSelect.selectedIndex];
        if (selectedUser && selectedUser.value) {
            previewUser.textContent = `| ${selectedUser.text}`;
        } else {
            previewUser.textContent = '';
        }
        
        const selectedStatus = statusSelect.options[statusSelect.selectedIndex];
        previewStatus.textContent = selectedStatus.text !== 'Pilih Status' ? selectedStatus.text : 'Status';
        
        // Update badge color based on status
        const statusValue = statusSelect.value;
        previewStatus.className = 'badge ' + (
            statusValue === 'aktif' ? 'badge-success' :
            statusValue === 'tidak_aktif' ? 'badge-danger' :
            statusValue === 'maintenance' ? 'badge-warning' : 'badge-secondary'
        );
    }
    
    // Event listeners for preview
    namaInput.addEventListener('input', updatePreview);
    serialInput.addEventListener('input', updatePreview);
    lembagaSelect.addEventListener('change', updatePreview);
    userSelect.addEventListener('change', updatePreview);
    statusSelect.addEventListener('change', updatePreview);
    
    // Initialize based on current lembaga
    handleLembagaChange();
});

function handleLembagaChange() {
    const lembagaSelect = document.getElementById('lembaga_id');
    const userSelect = document.getElementById('user_id');
    const userRequired = document.getElementById('user_required');
    const userHelp = document.getElementById('user_help');
    
    const selectedOption = lembagaSelect.options[lembagaSelect.selectedIndex];
    const lembagaId = selectedOption.value;
    const lembagaType = selectedOption.getAttribute('data-type');
    
    if (!lembagaId) {
        userRequired.style.display = 'none';
        userHelp.style.display = 'none';
        return;
    }
    
    // Show/hide required indicator based on lembaga type
    if (lembagaType === 'Individu') {
        userRequired.style.display = 'inline';
        userHelp.style.display = 'block';
        userSelect.required = true;
    } else {
        userRequired.style.display = 'none';
        userHelp.style.display = 'none';
        userSelect.required = false;
    }
    
    // Store current selected user
    const currentUserId = userSelect.value;
    
    // Fetch users for selected lembaga
    fetch(`/admin/manajemen-perangkat/users-by-lembaga?lembaga_id=${lembagaId}`)
        .then(response => response.json())
        .then(users => {
            userSelect.innerHTML = '<option value="">Pilih Pengguna</option>';
            
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.nama_lengkap} (${user.email})`;
                
                // Restore selected user if exists in new list
                if (user.id == currentUserId) {
                    option.selected = true;
                }
                
                userSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            userSelect.innerHTML = '<option value="">Error loading users</option>';
        });
}
</script>
@endsection