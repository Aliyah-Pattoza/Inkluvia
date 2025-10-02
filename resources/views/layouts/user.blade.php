<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Inkluvia</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#513587',
                            dark: '#3d2866',
                            light: '#6B46C1',
                        },
                        secondary: {
                            DEFAULT: '#A7AAFF',
                            light: '#C4B5FD',
                        }
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-['Inter']">
    <div class="min-h-screen flex">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-primary to-primary-dark text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
               role="navigation"
               aria-label="Navigasi Utama">
            <!-- Sidebar Header -->
            <div class="p-6 border-b border-white border-opacity-20">
                <h2 class="text-2xl font-bold">User</h2>
                <p class="text-sm text-white text-opacity-80 mt-1">Panel Pengguna</p>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="py-2" aria-label="Menu Navigasi">
                <a href="{{ route('dashboard') }}" 
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                aria-label="Dashboard"
                aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                    <i class="fas fa-home mr-3" aria-hidden="true"></i>
                    Dashboard
                </a>
                
                <a href="{{ route('user.jadwal-belajar') }}" 
                class="nav-link {{ request()->routeIs('user.jadwal-belajar*') ? 'active' : '' }}"
                aria-label="Jadwal Sesi Belajar">
                    <i class="fas fa-calendar-alt mr-3" aria-hidden="true"></i>
                    Jadwal Sesi Belajar
                </a>
                
                <a href="{{ route('user.materi-saya') }}" 
                class="nav-link {{ request()->routeIs('user.materi-saya*') ? 'active' : '' }}"
                aria-label="Materi Saya">
                    <i class="fas fa-folder-open mr-3" aria-hidden="true"></i>
                    Materi Saya
                </a>
                
                <a href="{{ route('user.perpustakaan') }}" 
                class="nav-link {{ request()->routeIs('user.perpustakaan') ? 'active' : '' }}"
                aria-label="Perpustakaan">
                    <i class="fas fa-book mr-3" aria-hidden="true"></i>
                    Perpustakaan
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-40" role="banner">
                <div class="flex items-center justify-between px-4 py-3 lg:px-8">
                    <!-- Mobile Menu Button & Logo -->
                    <div class="flex items-center space-x-4">
                        <button id="menuToggle" 
                                class="lg:hidden p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50"
                                aria-label="Toggle menu"
                                aria-expanded="false"
                                aria-controls="sidebar">
                            <i class="fas fa-bars text-xl text-gray-700" aria-hidden="true"></i>
                        </button>
                        
                        <a href="{{ route('home') }}" 
                           class="flex items-center text-primary font-bold text-xl focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 rounded"
                           aria-label="Kembali ke beranda Inkluvia">
                            <i class="fas fa-home mr-2" aria-hidden="true"></i>
                            <span>Inkluvia</span>
                        </a>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- User Info -->
                        <div class="hidden sm:flex items-center space-x-3" role="status" aria-label="Informasi Pengguna">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold" aria-hidden="true">
                                {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="hidden md:block">
                                <div class="font-semibold text-gray-900 text-sm">{{ Auth::user()->nama_lengkap }}</div>
                                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-sm font-medium"
                                    aria-label="Logout dari sistem">
                                <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div role="alert" 
                 class="mx-4 mt-4 lg:mx-8 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center"
                 aria-live="polite">
                <i class="fas fa-check-circle mr-3 text-green-600" aria-hidden="true"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div role="alert" 
                 class="mx-4 mt-4 lg:mx-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center"
                 aria-live="assertive">
                <i class="fas fa-exclamation-circle mr-3 text-red-600" aria-hidden="true"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div role="alert" 
                 class="mx-4 mt-4 lg:mx-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg"
                 aria-live="assertive">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600 mt-0.5" aria-hidden="true"></i>
                    <div>
                        <strong class="font-semibold">Terjadi kesalahan:</strong>
                        <ul class="mt-2 ml-4 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content -->
            <main class="flex-1 px-4 py-6 lg:px-8">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-4 lg:px-8 mt-auto" role="contentinfo">
                <div class="text-center text-sm text-gray-600">
                    <p>&copy; {{ date('Y') }} Inkluvia - Platform Pembelajaran Inklusif</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="overlay" 
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"
         aria-hidden="true"></div>

    <!-- Base Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            // Toggle Mobile Menu
            menuToggle?.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                
                this.setAttribute('aria-expanded', !isExpanded);
            });

            // Close menu when clicking overlay
            overlay?.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                menuToggle?.setAttribute('aria-expanded', 'false');
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });

            // Keyboard navigation support
            document.addEventListener('keydown', function(e) {
                // ESC to close mobile menu
                if (e.key === 'Escape') {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    menuToggle?.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>

    @stack('scripts')

    <style>
        /* Navigation Link Styles */
        .nav-link {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
            font-size: 0.9375rem;
            font-weight: 400;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active {
            background-color: rgba(167, 170, 255, 0.2);
            color: white;
            border-left-color: #A7AAFF;
            font-weight: 500;
        }

        .nav-link:focus {
            outline: 2px solid #A7AAFF;
            outline-offset: -2px;
        }

        /* Focus visible styles for accessibility */
        *:focus-visible {
            outline: 2px solid #A7AAFF;
            outline-offset: 2px;
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .nav-link.active {
                border-left-width: 6px;
            }
        }
    </style>
</body>
</html>