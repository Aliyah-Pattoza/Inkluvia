@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('admin.kelola-pengguna') }}" class="btn btn-secondary" style="margin-right: 1rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div>
            <h1 class="page-title">Edit Pengguna</h1>
            <p class="page-subtitle">Edit data pengguna {{ $user->nama_lengkap }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('admin.kelola-pengguna.update', $user) }}">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label for="name" class="form-label">Username <span style="color: var(--danger);">*</span></label>
                <input type="text" id="name" name="name" class="form-input" 
                       value="{{ old('name', $user->name) }}" required 
                       placeholder="Masukkan username">
            </div>
            
            <div class="form-group">
                <label for="nama_lengkap" class="form-label">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input" 
                       value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required 
                       placeholder="Masukkan nama lengkap">
            </div>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email <span style="color: var(--danger);">*</span></label>
            <input type="email" id="email" name="email" class="form-input" 
                   value="{{ old('email', $user->email) }}" required 
                   placeholder="Masukkan alamat email">
        </div>
        
        <div style="background: var(--gray-50); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <p style="color: var(--text-light); font-size: 0.875rem; margin-bottom: 1rem;">
                <i class="fas fa-info-circle"></i> Kosongkan password jika tidak ingin mengubahnya
            </p>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           minlength="8" 
                           placeholder="Minimal 8 karakter">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                           placeholder="Ulangi password baru">
                </div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span style="color: var(--danger);">*</span></label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-input" 
                       value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span style="color: var(--danger);">*</span></label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label for="lembaga_id" class="form-label">Lembaga <span style="color: var(--danger);">*</span></label>
                <select id="lembaga_id" name="lembaga_id" class="form-select" required>
                    <option value="">Pilih Lembaga</option>
                    @foreach($lembagas as $lembaga)
                    <option value="{{ $lembaga->id }}" {{ old('lembaga_id', $user->lembaga_id) == $lembaga->id ? 'selected' : '' }}>
                        {{ $lembaga->nama }} ({{ $lembaga->type }})
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="role" class="form-label">Role <span style="color: var(--danger);">*</span></label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Pilih Role</option>
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
        </div>
        
        <!-- User Info -->
        <div style="background: var(--gray-50); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <h4 style="margin-bottom: 0.5rem; color: var(--text-dark);">Informasi Pengguna</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.875rem; color: var(--text-light);">
                <div>
                    <strong>Bergabung:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong>Terakhir Update:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
        
        <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('admin.kelola-pengguna') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Pengguna
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePassword() {
        if (password.value && password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Password tidak sama');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePassword);
    passwordConfirmation.addEventListener('input', validatePassword);
});
</script>
@endsection