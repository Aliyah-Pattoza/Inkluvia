<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke platform pembelajaran inklusif Inkluvia">
    <title>Login - Inkluvia</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#513587',
                        secondary: '#A7AAFF'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-['Inter']">
    <!-- Skip to main content -->
    <a href="#login-form" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 bg-primary text-white px-4 py-2 rounded">
        Skip to login form
    </a>

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center text-3xl font-bold text-primary mb-8 hover:opacity-80 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded"
                   aria-label="Kembali ke beranda Inkluvia">
                    <svg class="w-10 h-10 mr-3" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="15" r="8" fill="#A7AAFF"/>
                        <path d="M20 25C13 25 8 30 8 35H32C32 30 27 25 20 25Z" fill="#513587"/>
                    </svg>
                    inkluvia
                </a>

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Login</h1>
                    <p class="text-gray-600">Masuk untuk melanjutkan pembelajaran</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                <div role="alert" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3" aria-hidden="true"></i>
                        <div class="flex-1">
                            <p class="text-sm text-red-800">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-6">
                    @csrf
                    
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400" aria-hidden="true"></i>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                autofocus
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                                placeholder="Email"
                                value="{{ old('email') }}"
                                aria-required="true"
                                aria-describedby="email-help">
                        </div>
                        <p id="email-help" class="sr-only">Masukkan alamat email Anda untuk login</p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400" aria-hidden="true"></i>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                                placeholder="Kata Sandi"
                                aria-required="true"
                                aria-describedby="password-help">
                        </div>
                        <p id="password-help" class="sr-only">Masukkan password Anda</p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                        aria-label="Login ke akun Anda">
                        Log In
                    </button>
                </form>

                <!-- Register Link -->
                <p class="mt-6 text-center text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" 
                       class="text-primary font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded"
                       aria-label="Daftar akun baru">
                        Register
                    </a>
                </p>
            </div>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden lg:flex flex-1 bg-gradient-to-br from-primary to-secondary items-center justify-center p-12">
            <div class="max-w-lg text-center" role="img" aria-label="Ilustrasi pembelajaran braille">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-3xl p-8 mb-6">
                    <svg class="w-full h-auto" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Chalkboard -->
                        <rect x="50" y="30" width="300" height="180" rx="8" fill="#2D8B7B"/>
                        <rect x="40" y="20" width="320" height="200" rx="8" stroke="#C6903B" stroke-width="8"/>
                        
                        <!-- Braille dots on board -->
                        <g fill="white">
                            <circle cx="120" cy="80" r="6"/>
                            <circle cx="140" cy="80" r="6"/>
                            <circle cx="120" cy="100" r="6"/>
                            <circle cx="140" cy="100" r="6"/>
                            <circle cx="120" cy="120" r="6"/>
                            <circle cx="140" cy="120" r="6"/>
                            
                            <circle cx="180" cy="80" r="6"/>
                            <circle cx="200" cy="80" r="6"/>
                            <circle cx="200" cy="100" r="6"/>
                            <circle cx="180" cy="120" r="6"/>
                            <circle cx="200" cy="120" r="6"/>
                            
                            <circle cx="240" cy="80" r="6"/>
                            <circle cx="260" cy="80" r="6"/>
                            <circle cx="240" cy="100" r="6"/>
                            <circle cx="260" cy="100" r="6"/>
                            <circle cx="240" cy="120" r="6"/>
                        </g>
                        
                        <!-- Teacher -->
                        <ellipse cx="300" cy="250" rx="30" ry="8" fill="#1F2937" opacity="0.2"/>
                        <rect x="285" y="180" width="30" height="60" rx="15" fill="#E8B4B8"/>
                        <circle cx="300" cy="165" r="18" fill="#E8B4B8"/>
                        <path d="M282 165C282 160 285 155 300 155C315 155 318 160 318 165" fill="#1F2937"/>
                        <circle cx="295" cy="162" r="3" fill="#1F2937"/>
                        <circle cx="305" cy="162" r="3" fill="#1F2937"/>
                        <rect x="285" y="178" width="30" height="35" fill="#DC2626"/>
                        <path d="M270 210L285 200L285 240L270 230Z" fill="#1F2937"/>
                        <path d="M315 200L330 210L330 230L315 240Z" fill="#1F2937"/>
                        
                        <!-- Student -->
                        <ellipse cx="120" cy="280" rx="28" ry="8" fill="#1F2937" opacity="0.2"/>
                        <rect x="107" y="220" width="26" height="50" rx="13" fill="#F9C6BC"/>
                        <circle cx="120" cy="205" r="16" fill="#F9C6BC"/>
                        <path d="M105 205C105 200 108 196 120 196C132 196 135 200 135 205" fill="#1F2937"/>
                        <circle cx="115" cy="203" r="2" fill="#1F2937"/>
                        <circle cx="125" cy="203" r="2" fill="#1F2937"/>
                        <rect x="107" y="218" width="26" height="30" fill="#A78BFA"/>
                        <rect x="95" y="235" width="12" height="35" fill="#1E3A8A"/>
                        <rect x="133" y="235" width="12" height="35" fill="#1E3A8A"/>
                        <rect x="107" y="245" width="26" height="8" fill="#1F2937"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Platform Pembelajaran Inklusif</h2>
                <p class="text-white text-opacity-90">Belajar Braille dengan mudah dan interaktif untuk semua</p>
            </div>
        </div>
    </div>

    <!-- Live Region for Screen Reader Announcements -->
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

    <style>
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
        
        .sr-only.focus\:not-sr-only:focus {
            position: static;
            width: auto;
            height: auto;
            padding: 0.5rem 1rem;
            margin: 0;
            overflow: visible;
            clip: auto;
            white-space: normal;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus management
            const emailInput = document.getElementById('email');
            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }

            // Form submission announcement
            const form = document.getElementById('login-form');
            form.addEventListener('submit', function() {
                document.getElementById('announcements').textContent = 'Memproses login...';
            });

            // Announce page load for screen readers
            setTimeout(() => {
                document.getElementById('announcements').textContent = 'Halaman login. Masukkan email dan password untuk login.';
            }, 500);
        });
    </script>
</body>
</html>