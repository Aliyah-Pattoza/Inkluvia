@extends('layouts.admin')

@section('title', 'Tambah Perangkat')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('admin.kelola-perangkat') }}" class="btn btn-secondary" style="margin-right: 1rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div>
            <h1 class="page-title">Tambah Perangkat Baru</h1>
            <p class="page-subtitle">Tambahkan perangkat EduBraille baru ke sistem</p>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('admin.kelola-perangkat.store') }}" id="deviceForm">
        @csrf
        
        <div class="form-group">
            <label for="nama_device" class="form-label">Nama Perangkat <span style="color: var(--danger);">*</span></label>
            <input type="text" id="nama_device" name="nama_device" class="form-input" 
                   value="{{ old('nama_device') }}" required 
                   placeholder="Contoh: EduBraille Lab 1">
        </div>
        
        <div class="form-group">
            <label for="serial_number" class="form-label">Serial Number</label>
            <input type="text" id="serial_number" name="serial_number" class="form-input" 
                   value="{{ old('serial_number') }}" 
                   placeholder="Kosongkan untuk generate otomatis">
            <small style="color: var(--text-light); font-size: 0.875rem;">
                Format: EDU + 6 digit hex (contoh: EDU1A2B3C). Kosongkan untuk generate otomatis.
            </small>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label for="lembaga_id" class="form-label">Lembaga <span style="color: var(--danger);">*</span></label>
                <select id="lembaga_id" name="lembaga_id" class="form-select" required onchange="handleLembagaChange()">
                    <option value="">Pilih Lembaga</option>
                    @foreach($lembagas as $lembaga)
                    <option value="{{ $lembaga->id }}" data-type="{{ $lembaga->type }}" {{ old('lembaga_id') == $lembaga->id ? 'selected' : '' }}>
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
                <select id="user_id" name="user_id" class="form-select" disabled>
                    <option value="">Pilih lembaga terlebih dahulu</option>
                </select>
                <small id="user_help" style="color: var(--text-light); font-size: 0.875rem; display: none;">
                    Wajib diisi untuk lembaga individu
                </small>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label for="status" class="form-label">Status <span style="color: var(--danger);">*</span></label>
                <select id="status" name="status" class="form-select" required>
                    <option value="">Pilih Status</option>
                    <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea id="keterangan" name="keterangan" class="form-input" rows="3" 
                      placeholder="Keterangan tambahan tentang perangkat">{{ old('keterangan') }}</textarea>
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
                        <div style="font-weight: 600;" id="preview-nama">Nama Perangkat</div>
                        <div style="font-size: 0.875rem; color: var(--text-light);">
                            <code id="preview-serial" style="background: var(--gray-100); padding: 0.125rem 0.25rem; border-radius: 4px;">Serial Number</code>
                        </div>
                        <div style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.25rem;">
                            <span id="preview-lembaga">Lembaga</span>
                            <span id="preview-user" style="margin-left: 0.5rem;"></span>
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <span class="badge badge-secondary" id="preview-status">Status</span>
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
                <i class="fas fa-save"></i> Simpan Perangkat
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
    
    // Initialize preview
    updatePreview();
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
        userSelect.disabled = true;
        userSelect.innerHTML = '<option value="">Pilih lembaga terlebih dahulu</option>';
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
    
    // Fetch users for selected lembaga
    fetch(`/admin/manajemen-perangkat/users-by-lembaga?lembaga_id=${lembagaId}`)
        .then(response => response.json())
        .then(users => {
            userSelect.disabled = false;
            userSelect.innerHTML = '<option value="">Pilih Pengguna</option>';
            
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.nama_lengkap} (${user.email})`;
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