@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Manajemen Pengguna</h1>
            <p class="page-subtitle">Kelola pengguna sistem Inkluvia</p>
        </div>
        <a href="{{ route('admin.kelola-pengguna.create') }}" class="btn btn-primary">
            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
            Tambah Pengguna
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" action="{{ route('admin.kelola-pengguna') }}">
        <div class="filters-grid">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search" class="form-label">Cari Pengguna</label>
                <input type="text" id="search" name="search" class="form-input" 
                       placeholder="Nama, email..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="role" class="form-label">Filter Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
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
                <label for="sort_by" class="form-label">Urutkan</label>
                <select id="sort_by" name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="nama_lengkap" {{ request('sort_by') === 'nama_lengkap' ? 'selected' : '' }}>Nama</option>
                    <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="role" {{ request('sort_by') === 'role' ? 'selected' : '' }}>Role</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">&nbsp;</label>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.kelola-pengguna') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
    </form>
</div>

<!-- Users Table -->
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nama_lengkap', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Nama Lengkap
                        @if(request('sort_by') === 'nama_lengkap')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Email
                        @if(request('sort_by') === 'email')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>Lembaga</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'role', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Role
                        @if(request('sort_by') === 'role')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>Jenis Kelamin</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Bergabung
                        @if(request('sort_by') === 'created_at')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div style="display: flex; align-items: center;">
                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.75rem; margin-right: 0.75rem;">
                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $user->nama_lengkap }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ $user->name }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->lembaga->nama ?? '-' }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-success' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>{{ $user->jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.kelola-pengguna.edit', $user) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.kelola-pengguna.destroy', $user) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-light);">
                    <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; display: block;"></i>
                    Tidak ada pengguna ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($users->hasPages())
<div class="pagination">
    {{ $users->links() }}
</div>
@endif

<!-- Summary -->
<div style="margin-top: 1rem; text-align: center; color: var(--text-light); font-size: 0.875rem;">
    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
</div>
@endsection