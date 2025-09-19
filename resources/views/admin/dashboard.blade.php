@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admin Dashboard</h1>
    <p class="page-subtitle">Kelola platform pembelajaran inklusif Inkluvia</p>
</div>

<!-- Welcome Card -->
<div class="welcome-card">
    <h2>Selamat Datang, {{ Auth::user()->nama_lengkap }}!</h2>
    <p>Kelola dan pantau seluruh aktivitas platform pembelajaran inklusif Inkluvia</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Pengguna</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-building"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['total_lembagas'] }}</div>
            <div class="stat-label">Total Lembaga</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-laptop"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['active_devices'] }}</div>
            <div class="stat-label">Perangkat Aktif</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-book"></i>
        </div>
        <div>
            <div class="stat-number">{{ $stats['total_materials'] }}</div>
            <div class="stat-label">Total Materi</div>
        </div>
    </div>
</div>

<!-- Admin Actions -->
<div class="card">
    <h3 style="margin-bottom: 1.5rem; color: var(--text-dark); font-size: 1.25rem; font-weight: 600;">Panel Administrasi</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('admin.kelola-pengguna') }}" class="btn btn-primary" style="justify-content: center; flex-direction: column; height: 120px; text-align: center;">
            <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            Manajemen Pengguna
        </a>
        
        <a href="{{ route('admin.manajemen-lembaga') }}" class="btn btn-secondary" style="justify-content: center; flex-direction: column; height: 120px; text-align: center;">
            <i class="fas fa-building" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            Manajemen Lembaga
        </a>
        
        <button class="btn btn-secondary" style="justify-content: center; flex-direction: column; height: 120px; text-align: center;" onclick="alert('Fitur sedang dalam pengembangan')">
            <i class="fas fa-laptop" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            Manajemen Perangkat
        </button>
        
        <a href="{{ route('admin.manajemen-materi') }}" class="btn btn-secondary" style="justify-content: center; flex-direction: column; height: 120px; text-align: center;">
            <i class="fas fa-braille" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            Manajemen Materi
        </a>
        
        <a href="{{ route('admin.request-materi') }}" class="btn btn-info" style="justify-content: center; flex-direction: column; height: 120px; text-align: center;">
            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            Request Materi
        </a>
    </div>
</div>

<!-- User Statistics -->
@if(count($stats['users_by_role']) > 0)
<div class="card">
    <h3 style="margin-bottom: 1.5rem; color: var(--text-dark); font-size: 1.25rem; font-weight: 600;">Statistik Pengguna</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
        @foreach($stats['users_by_role'] as $role => $count)
        <div style="text-align: center; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">{{ $count }}</div>
            <div style="font-size: 0.875rem; color: var(--text-light); text-transform: capitalize;">{{ $role }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recent Users -->
@if($stats['recent_users']->count() > 0)
<div class="card">
    <h3 style="margin-bottom: 1.5rem; color: var(--text-dark); font-size: 1.25rem; font-weight: 600;">Pengguna Terbaru</h3>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Lembaga</th>
                    <th>Role</th>
                    <th>Bergabung</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['recent_users'] as $user)
                <tr>
                    <td>{{ $user->nama_lengkap }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->lembaga->nama ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-success' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Recent Activity -->
<div class="card">
    <h3 style="margin-bottom: 1.5rem; color: var(--text-dark); font-size: 1.25rem; font-weight: 600;">Aktivitas Terbaru</h3>
    <div style="text-align: center; padding: 2rem; color: var(--text-light);">
        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <p>Belum ada aktivitas terbaru</p>
    </div>
</div>
@endsection