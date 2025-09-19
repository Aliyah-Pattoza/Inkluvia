<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inkluvia adalah platform pendidikan inklusif berbasis web untuk tunanetra">
    <title>@yield('title', 'Inkluvia - Platform Edukasi Inklusif')</title>
    <link rel="icon" href="{{ asset('assets/icon.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">User</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    Dashboard
                </a>
                <a href="javascript:void(0)" class="nav-item" onclick="alert('Fitur sedang dalam pengembangan'); return false;">
                    <i class="fas fa-calendar-alt"></i>
                    Jadwal Belajar
                </a>
                <a href="javascript:void(0)" class="nav-item" onclick="alert('Fitur sedang dalam pengembangan'); return false;">
                    <i class="fas fa-plus-circle"></i>
                    Request Materi
                </a>
                <a href="{{ route('user.perpustakaan') }}" class="nav-item {{ request()->routeIs('user.perpustakaan*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    Perpustakaan
                </a>
                <a href="javascript:void(0)" class="nav-item" onclick="alert('Fitur sedang dalam pengembangan'); return false;">
                    <i class="fas fa-list"></i>
                    Request Saya
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <a href="{{ route('home') }}" class="logo">
                        <i class="fas fa-home" style="margin-right: 0.5rem;"></i>
                        Inkluvia
                    </a>
                </div>
                <div class="topbar-right">
                    <div class="user-menu">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->nama_lengkap ?? Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ Auth::user()->email }}</div>
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
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            // Close sidebar when clicking overlay (mobile)
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    mobileOverlay.style.display = 'none';
                });
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (!isMobile()) {
                    sidebar.classList.remove('show');
                    if (mobileOverlay) {
                        mobileOverlay.style.display = 'none';
                    }
                }
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
