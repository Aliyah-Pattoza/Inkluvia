{{-- resources/views/user/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inkluvia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">User</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="{{ route('user.jadwal-belajar') }}" class="nav-item">
                    <i class="fas fa-calendar-alt"></i>
                    Jadwal Belajar
                </a>
                <a href="{{ route('user.request-materi') }}" class="nav-item">
                    <i class="fas fa-file-circle-plus"></i>
                    Request Materi
                </a>
                <a href="{{ route('user.perpustakaan') }}" class="nav-item">
                    <i class="fas fa-book"></i>
                    Perpustakaan
                </a>
                <a href="{{ route('user.my-requests') }}" class="nav-item">
                    <i class="fas fa-list"></i>
                    Request Saya
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <a href="{{ route('home') }}" class="logo" style="color: var(--primary-color); text-decoration: none; font-weight: 700; font-size: 1.25rem;">
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
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin-left: 1rem;">
                        @csrf
                        <button type="submit" style="
                            background: var(--primary-color);
                            color: white;
                            border: none;
                            padding: 0.5rem 1rem;
                            border-radius: 8px;
                            cursor: pointer;
                            font-size: 0.875rem;
                            transition: all 0.2s;
                        " onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Selamat datang di platform pembelajaran inklusif Inkluvia</p>
                </div>

                <!-- Quick Actions -->
                <div style="background: var(--white); padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border: 1px solid var(--border-color); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--text-dark); font-size: 1.25rem; font-weight: 600;">Aksi Cepat</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <a href="{{ route('user.perpustakaan') }}" style="
                            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                            color: white;
                            border: none;
                            padding: 1.5rem;
                            border-radius: 12px;
                            cursor: pointer;
                            font-size: 1rem;
                            font-weight: 600;
                            transition: all 0.2s;
                            text-decoration: none;
                            display: block;
                            text-align: center;
                        " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <i class="fas fa-play" style="display: block; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                            Mulai Belajar
                        </a>
                        
                        <a href="javascript:void(0)" onclick="alert('Fitur sedang dalam pengembangan'); return false;" style="
                            background: var(--white);
                            color: var(--primary-color);
                            border: 2px solid var(--primary-color);
                            padding: 1.5rem;
                            border-radius: 12px;
                            cursor: pointer;
                            font-size: 1rem;
                            font-weight: 600;
                            transition: all 0.2s;
                            text-decoration: none;
                            display: block;
                            text-align: center;
                        " onmouseover="this.style.background='var(--primary-color)'; this.style.color='white'" onmouseout="this.style.background='var(--white)'; this.style.color='var(--primary-color)'">
                            <i class="fas fa-file-circle-plus" style="display: block; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                            Request Materi
                        </a>
                        
                        <a href="javascript:void(0)" onclick="alert('Fitur sedang dalam pengembangan'); return false;" style="
                            background: var(--white);
                            color: var(--primary-color);
                            border: 2px solid var(--primary-color);
                            padding: 1.5rem;
                            border-radius: 12px;
                            cursor: pointer;
                            font-size: 1rem;
                            font-weight: 600;
                            transition: all 0.2s;
                            text-decoration: none;
                            display: block;
                            text-align: center;
                        " onmouseover="this.style.background='var(--primary-color)'; this.style.color='white'" onmouseout="this.style.background='var(--white)'; this.style.color='var(--primary-color)'">
                            <i class="fas fa-list" style="display: block; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                            Request Saya
                        </a>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">Materi Dipelajari</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">Jadwal Hari Ini</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">Request Materi</div>
                    </div>
                </div>             
            </div>
        </div>
    </div>

    <script>
        // Sidebar navigation handling
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation is now handled by proper links
            // No need for alert handling
        });
    </script>
</body>
</html>