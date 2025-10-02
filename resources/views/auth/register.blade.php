<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar akun baru di platform pembelajaran inklusif Inkluvia">
    <title>Register - Inkluvia</title>
    
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
    <a href="#register-form" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 bg-primary text-white px-4 py-2 rounded">
        Skip to registration form
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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Bergabung dengan Inkluvia</h1>
                    <p class="text-gray-600">Daftar untuk memulai perjalanan belajar Anda</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                <div role="alert" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3" aria-hidden="true"></i>
                        <div class="flex-1">
                            <ul class="text-sm text-red-800 space-y-1">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" id="register-form" class="space-y-5">
                    @csrf
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-semibold text-gray-900 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama_lengkap" 
                            name="nama_lengkap" 
                            required
                            autofocus
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Masukkan nama lengkap"
                            value="{{ old('nama_lengkap') }}"
                            aria-required="true">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Masukkan email"
                            value="{{ old('email') }}"
                            aria-required="true">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="tanggal_lahir" 
                            name="tanggal_lahir" 
                            required
                            max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            value="{{ old('tanggal_lahir') }}"
                            aria-required="true">
                    </div>

                    <!-- Jenis Kelamin -->
                    <fieldset>
                        <legend class="block text-sm font-semibold text-gray-900 mb-3">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </legend>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="relative">
                                <input 
                                    type="radio" 
                                    id="laki_laki" 
                                    name="jenis_kelamin" 
                                    value="L"
                                    {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }}
                                    required
                                    class="peer sr-only">
                                <label for="laki_laki" 
                                       class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                    <i class="fas fa-mars text-primary mr-2" aria-hidden="true"></i>
                                    <span class="font-medium text-gray-900">Laki-laki</span>
                                    <i class="fas fa-check-circle text-primary ml-auto opacity-0 peer-checked:opacity-100 transition-opacity" aria-hidden="true"></i>
                                </label>
                            </div>
                            
                            <div class="relative">
                                <input 
                                    type="radio" 
                                    id="perempuan" 
                                    name="jenis_kelamin" 
                                    value="P"
                                    {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }}
                                    class="peer sr-only">
                                <label for="perempuan" 
                                       class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                    <i class="fas fa-venus text-primary mr-2" aria-hidden="true"></i>
                                    <span class="font-medium text-gray-900">Perempuan</span>
                                    <i class="fas fa-check-circle text-primary ml-auto opacity-0 peer-checked:opacity-100 transition-opacity" aria-hidden="true"></i>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Lembaga -->
                    <div>
                        <label for="lembaga_id" class="block text-sm font-semibold text-gray-900 mb-2">
                            Sekolah/Lembaga <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="lembaga_id" 
                            name="lembaga_id" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base bg-white"
                            aria-required="true">
                            <option value="">Pilih Sekolah/Lembaga</option>
                            @foreach($lembagas as $lembaga)
                            <option value="{{ $lembaga->id }}" {{ old('lembaga_id') == $lembaga->id ? 'selected' : '' }}>
                                {{ $lembaga->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Minimal 8 karakter"
                            aria-required="true"
                            aria-describedby="password-help">
                        <p id="password-help" class="mt-1 text-xs text-gray-500">
                            Password minimal 8 karakter
                        </p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Ulangi password"
                            aria-required="true">
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                        aria-label="Daftar akun baru">
                        <i class="fas fa-user-plus mr-2" aria-hidden="true"></i>
                        Daftar Sekarang
                    </button>
                </form>

                <!-- Login Link -->
                <p class="mt-6 text-center text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" 
                       class="text-primary font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded"
                       aria-label="Login ke akun yang sudah ada">
                        Masuk di sini
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
                <h2 class="text-2xl font-bold text-white mb-3">Mulai Perjalanan Belajar Anda</h2>
                <p class="text-white text-opacity-90">Bergabung dengan ribuan pelajar lainnya dalam platform pembelajaran inklusif</p>
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
            const namaInput = document.getElementById('nama_lengkap');
            if (namaInput && !namaInput.value) {
                namaInput.focus();
            }

            // Password confirmation validation
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            
            function validatePassword() {
                if (password.value && passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                    passwordConfirmation.setCustomValidity('Password tidak sama');
                } else {
                    passwordConfirmation.setCustomValidity('');
                }
            }
            
            password.addEventListener('input', validatePassword);
            passwordConfirmation.addEventListener('input', validatePassword);

            // Form submission announcement
            const form = document.getElementById('register-form');
            form.addEventListener('submit', function() {
                document.getElementById('announcements').textContent = 'Memproses pendaftaran...';
            });

            // Announce page load
            setTimeout(() => {
                document.getElementById('announcements').textContent = 'Halaman pendaftaran. Isi formulir untuk membuat akun baru.';
            }, 500);
        });
    </script>
</body>
</html>