<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Inkluvia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Admin</h2>
                <p class="sidebar-subtitle">Panel Administrasi</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.kelola-pengguna') }}" class="nav-item {{ request()->routeIs('admin.kelola-pengguna*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Manajemen Pengguna
                </a>
                <a href="{{ route('admin.manajemen-lembaga') }}" class="nav-item {{ request()->routeIs('admin.manajemen-lembaga*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    Manajemen Lembaga
                </a>
                <a href="{{ route('admin.kelola-perangkat') }}" class="nav-item {{ request()->routeIs('admin.kelola-perangkat*') ? 'active' : '' }}">
                    <i class="fas fa-laptop"></i>
                    Manajemen Device
                </a>
                <a href="{{ route('admin.manajemen-materi') }}" class="nav-item {{ request()->routeIs('admin.manajemen-materi*') ? 'active' : '' }}">
                    <i class="fas fa-braille"></i>
                    Manajemen Materi
                </a>
                <a href="{{ route('admin.request-materi') }}" class="nav-item {{ request()->routeIs('admin.request-materi*') ? 'active' : '' }}">
                    <i class="fas fa-inbox"></i>
                    Request Materi
                </a>
                <a href="#" class="nav-item" onclick="alert('Fitur sedang dalam pengembangan')">
                    <i class="fas fa-cogs"></i>
                    Pengaturan
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="{{ route('home') }}" class="logo">
                        <i class="fas fa-home" style="margin-right: 0.5rem;"></i>
                        Inkluvia
                    </a>
                </div>
                <div class="topbar-right">
                    <div class="user-menu">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ Auth::user()->nama_lengkap }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);">Administrator</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin-left: 1rem;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                    {{ session('error') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-error">
                    <div>
                        <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    "></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            // Check if mobile
            function isMobile() {
                return window.innerWidth <= 768;
            }
            
            // Toggle sidebar
            function toggleSidebar() {
                if (isMobile()) {
                    sidebar.classList.toggle('show');
                    mobileOverlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
                } else {
                    sidebar.classList.toggle('hidden');
                    mainContent.classList.toggle('expanded');
                }
            }
            
            sidebarToggle.addEventListener('click', toggleSidebar);
            
            // Close sidebar when clicking overlay (mobile)
            mobileOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                mobileOverlay.style.display = 'none';
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (!isMobile()) {
                    sidebar.classList.remove('show');
                    mobileOverlay.style.display = 'none';
                }
            });
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>