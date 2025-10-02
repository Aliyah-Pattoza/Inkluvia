@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<!-- Skip to main content link for screen readers -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 bg-primary text-white px-4 py-2 rounded">
    Skip to main content
</a>

<!-- Voice Command Toggle (Sticky) -->
<button id="voiceToggle" 
        class="fixed bottom-6 right-6 z-50 w-16 h-16 bg-gradient-to-br from-primary to-secondary text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group"
        aria-label="Toggle voice commands"
        aria-pressed="false"
        title="Tekan untuk mengaktifkan perintah suara">
    <i class="fas fa-microphone text-2xl" id="micIcon"></i>
    <span class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full animate-pulse hidden" id="listeningIndicator"></span>
</button>

<!-- Voice Command Help -->
<div id="voiceHelp" class="hidden fixed bottom-24 right-6 bg-white rounded-lg shadow-xl p-4 max-w-xs z-40 border-2 border-primary">
    <h3 class="font-bold text-primary mb-2">Perintah Suara:</h3>
    <ul class="text-sm space-y-1 text-gray-700">
        <li>• "Buka perpustakaan"</li>
        <li>• "Request materi"</li>
        <li>• "Lihat jadwal"</li>
        <li>• "Bantuan suara"</li>
        <li>• "Tutup bantuan"</li>
    </ul>
</div>

<!-- Main Content -->
<main id="main-content" role="main" tabindex="-1">
    <!-- Page Header -->
    <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Selamat Datang, {{ Auth::user()->nama_lengkap }}
        </h1>
        <p class="text-gray-600">Platform pembelajaran inklusif untuk semua</p>
    </header>

    <!-- Quick Actions -->
    <section aria-label="Aksi Cepat" class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Mulai Belajar -->
            <a href="{{ route('user.perpustakaan') }}" 
               class="group bg-gradient-to-br from-primary to-secondary text-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 focus:ring-4 focus:ring-primary focus:ring-opacity-50 focus:outline-none"
               aria-label="Mulai belajar dari perpustakaan materi">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-play text-3xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Mulai Belajar</h3>
                    <p class="text-sm opacity-90">Akses materi pembelajaran</p>
                </div>
            </a>

            <!-- Request Materi -->
            <button onclick="handleRequestMateri()" 
                    class="group bg-white border-2 border-primary text-primary p-6 rounded-xl shadow-md hover:bg-primary hover:text-white transition-all duration-300 transform hover:-translate-y-1 focus:ring-4 focus:ring-primary focus:ring-opacity-50 focus:outline-none"
                    aria-label="Ajukan permintaan materi baru">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-primary bg-opacity-10 group-hover:bg-white group-hover:bg-opacity-20 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-circle-plus text-3xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Request Materi</h3>
                    <p class="text-sm opacity-90">Ajukan materi baru</p>
                </div>
            </button>

            <!-- Request Saya -->
            <button onclick="handleMyRequests()" 
                    class="group bg-white border-2 border-primary text-primary p-6 rounded-xl shadow-md hover:bg-primary hover:text-white transition-all duration-300 transform hover:-translate-y-1 focus:ring-4 focus:ring-primary focus:ring-opacity-50 focus:outline-none"
                    aria-label="Lihat daftar request materi saya">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-primary bg-opacity-10 group-hover:bg-white group-hover:bg-opacity-20 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-list text-3xl" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Request Saya</h3>
                    <p class="text-sm opacity-90">Lihat status request</p>
                </div>
            </button>
        </div>
    </section>

    <!-- Stats Grid -->
    <section aria-label="Statistik Pembelajaran" class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Statistik Saya</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Materi Dipelajari -->
            <article class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-book-open text-white text-2xl" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-3xl font-bold text-gray-900" aria-label="0 materi dipelajari">0</div>
                        <div class="text-gray-600 text-sm truncate">Materi Dipelajari</div>
                    </div>
                </div>
            </article>

            <!-- Jadwal Hari Ini -->
            <article class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-calendar-check text-white text-2xl" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-3xl font-bold text-gray-900" aria-label="0 jadwal hari ini">0</div>
                        <div class="text-gray-600 text-sm truncate">Jadwal Hari Ini</div>
                    </div>
                </div>
            </article>

            <!-- Request Materi -->
            <article class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-file-alt text-white text-2xl" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-3xl font-bold text-gray-900" aria-label="0 request materi">0</div>
                        <div class="text-gray-600 text-sm truncate">Request Materi</div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Upcoming Schedule -->
    <section aria-label="Jadwal Mendatang" class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Jadwal Mendatang</h2>
            <button onclick="handleJadwal()" 
                    class="text-primary hover:text-primary-dark font-medium text-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 rounded px-3 py-1"
                    aria-label="Lihat semua jadwal">
                Lihat Semua <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
            </button>
        </div>
        <div class="bg-white rounded-xl shadow-md p-8 text-center border border-gray-100">
            <i class="fas fa-calendar-alt text-gray-300 text-5xl mb-4" aria-hidden="true"></i>
            <p class="text-gray-500 mb-2">Belum ada jadwal hari ini</p>
            <p class="text-sm text-gray-400">Jadwal pembelajaran Anda akan muncul di sini</p>
        </div>
    </section>

    <!-- Recent Materials -->
    <section aria-label="Materi Terbaru">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Materi Terbaru</h2>
            <a href="{{ route('user.perpustakaan') }}" 
               class="text-primary hover:text-primary-dark font-medium text-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 rounded px-3 py-1"
               aria-label="Lihat semua materi di perpustakaan">
                Lihat Semua <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
            </a>
        </div>
        <div class="bg-white rounded-xl shadow-md p-8 text-center border border-gray-100">
            <i class="fas fa-book text-gray-300 text-5xl mb-4" aria-hidden="true"></i>
            <p class="text-gray-500 mb-2">Belum ada materi tersedia</p>
            <p class="text-sm text-gray-400">Materi pembelajaran akan muncul di sini</p>
        </div>
    </section>
</main>

<!-- Live Region for Screen Reader Announcements -->
<div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

@push('styles')
<style>
    /* Screen Reader Only Class */
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

    /* High Contrast Mode Support */
    @media (prefers-contrast: high) {
        .bg-gradient-to-br {
            background: var(--primary-color) !important;
        }
    }

    /* Reduced Motion Support */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Voice Command Animation */
    @keyframes pulse-ring {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    .listening {
        animation: pulse-ring 1.5s infinite;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Voice Command System
    let recognition = null;
    let isListening = false;
    let navbarAnnounced = false;

    // Initialize Speech Recognition
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'id-ID';
        recognition.continuous = false;
        recognition.interimResults = false;

        recognition.onstart = function() {
            isListening = true;
            document.getElementById('voiceToggle').setAttribute('aria-pressed', 'true');
            document.getElementById('listeningIndicator').classList.remove('hidden');
            document.getElementById('voiceToggle').classList.add('listening');
            speak('Mendengarkan perintah Anda');
        };

        recognition.onend = function() {
            isListening = false;
            document.getElementById('voiceToggle').setAttribute('aria-pressed', 'false');
            document.getElementById('listeningIndicator').classList.add('hidden');
            document.getElementById('voiceToggle').classList.remove('listening');
        };

        recognition.onresult = function(event) {
            const command = event.results[0][0].transcript.toLowerCase();
            console.log('Voice command:', command);
            handleVoiceCommand(command);
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            speak('Maaf, terjadi kesalahan. Silakan coba lagi.');
        };
    }

    // Voice Toggle Button
    document.getElementById('voiceToggle').addEventListener('click', function() {
        if (!recognition) {
            speak('Maaf, browser Anda tidak mendukung perintah suara');
            return;
        }

        if (isListening) {
            recognition.stop();
        } else {
            recognition.start();
        }
    });

    // Keyboard shortcut for voice command (Ctrl + Space)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.code === 'Space') {
            e.preventDefault();
            document.getElementById('voiceToggle').click();
        }
    });

    // Voice Command Handler
    function handleVoiceCommand(command) {
        if (command.includes('perpustakaan') || command.includes('belajar')) {
            speak('Membuka perpustakaan');
            setTimeout(() => window.location.href = '{{ route("user.perpustakaan") }}', 500);
        } else if (command.includes('request') || command.includes('materi')) {
            speak('Membuka halaman request materi');
            handleRequestMateri();
        } else if (command.includes('jadwal')) {
            speak('Membuka jadwal belajar');
            handleJadwal();
        } else if (command.includes('bantuan') || command.includes('help')) {
            toggleVoiceHelp();
        } else if (command.includes('tutup')) {
            document.getElementById('voiceHelp').classList.add('hidden');
            speak('Bantuan ditutup');
        } else {
            speak('Perintah tidak dikenali. Katakan bantuan suara untuk melihat daftar perintah.');
        }
    }

    // Text to Speech
    function speak(text) {
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.9;
            window.speechSynthesis.cancel(); // Cancel any ongoing speech
            window.speechSynthesis.speak(utterance);
        }
        
        // Also update live region for screen readers
        document.getElementById('announcements').textContent = text;
    }

    // Toggle Voice Help
    function toggleVoiceHelp() {
        const helpEl = document.getElementById('voiceHelp');
        if (helpEl.classList.contains('hidden')) {
            helpEl.classList.remove('hidden');
            speak('Menampilkan bantuan perintah suara');
        } else {
            helpEl.classList.add('hidden');
            speak('Menyembunyikan bantuan perintah suara');
        }
    }

    // Navigation announcement (only once)
    if (!sessionStorage.getItem('navbarAnnounced')) {
        setTimeout(() => {
            speak('Selamat datang di dashboard Inkluvia. Tekan Control + Spasi untuk mengaktifkan perintah suara.');
            sessionStorage.setItem('navbarAnnounced', 'true');
        }, 1000);
    }

    // Focus management
    document.getElementById('main-content').focus();

    // Global functions for buttons
    window.handleRequestMateri = function() {
        speak('Fitur request materi sedang dalam pengembangan');
        alert('Fitur sedang dalam pengembangan');
    };

    window.handleMyRequests = function() {
        speak('Fitur request saya sedang dalam pengembangan');
        alert('Fitur sedang dalam pengembangan');
    };

    window.handleJadwal = function() {
        speak('Fitur jadwal sedang dalam pengembangan');
        alert('Fitur sedang dalam pengembangan');
    };
});
</script>
@endpush
@endsection