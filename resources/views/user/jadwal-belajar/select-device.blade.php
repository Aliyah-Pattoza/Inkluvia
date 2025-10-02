@extends('layouts.user')

@section('title', 'Pilih Perangkat EduBraille')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('user.jadwal-belajar') }}" 
           class="inline-flex items-center text-primary hover:text-primary-dark font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded px-2 py-1"
           aria-label="Kembali ke daftar jadwal">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
            Kembali
        </a>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Pilih Perangkat EduBraille</h1>
        <p class="text-gray-600">Pilih satu atau lebih perangkat untuk mengirim materi pembelajaran</p>
    </div>

    <!-- Jadwal Info Card -->
    <div class="bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl p-6 mb-6 shadow-lg">
        <h2 class="text-xl font-bold mb-2">{{ $jadwal->judul }}</h2>
        <div class="space-y-1 text-sm opacity-90">
            <div>
                <i class="far fa-calendar mr-2" aria-hidden="true"></i>
                {{ $jadwal->tanggal->format('d F Y') }}
            </div>
            <div>
                <i class="far fa-clock mr-2" aria-hidden="true"></i>
                {{ $jadwal->waktu_mulai->format('H:i') }} - {{ $jadwal->waktu_selesai->format('H:i') }}
            </div>
            @if($jadwal->materi)
            <div>
                <i class="fas fa-book mr-2" aria-hidden="true"></i>
                {{ $jadwal->materi }}
            </div>
            @endif
        </div>
    </div>

    <!-- Device Selection Form -->
    <form method="POST" action="{{ route('user.jadwal-belajar.send', $jadwal) }}" id="deviceForm">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Daftar Perangkat Tersedia
            </h2>

            @if($devices->count() > 0)
            <fieldset>
                <legend class="sr-only">Pilih perangkat EduBraille</legend>
                <div class="space-y-3" role="group" aria-label="Daftar perangkat EduBraille">
                    @foreach($devices as $device)
                    <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center h-5">
                            <input 
                                id="device_{{ $device->id }}" 
                                name="devices[]" 
                                type="checkbox" 
                                value="{{ $device->id }}"
                                class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                aria-describedby="device_{{ $device->id }}_description">
                        </div>
                        <div class="ml-4 flex-1">
                            <label for="device_{{ $device->id }}" class="font-medium text-gray-900 cursor-pointer">
                                {{ $device->nama_device }}
                                @if($device->keterangan)
                                <span class="text-gray-600">({{ $device->keterangan }})</span>
                                @endif
                            </label>
                            <p id="device_{{ $device->id }}_description" class="text-sm text-gray-500 mt-1">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-laptop text-primary mr-2" aria-hidden="true"></i>
                                    Serial: {{ $device->serial_number }}
                                </span>
                                @if($device->isOnline())
                                <span class="ml-4 inline-flex items-center text-green-600">
                                    <i class="fas fa-circle text-xs mr-1" aria-hidden="true"></i>
                                    Online
                                </span>
                                @else
                                <span class="ml-4 inline-flex items-center text-gray-400">
                                    <i class="fas fa-circle text-xs mr-1" aria-hidden="true"></i>
                                    Offline
                                </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </fieldset>

            <!-- Select All -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center">
                    <input 
                        id="selectAll" 
                        type="checkbox"
                        class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        aria-label="Pilih semua perangkat">
                    <label for="selectAll" class="ml-3 font-medium text-gray-700 cursor-pointer">
                        Pilih Semua Perangkat
                    </label>
                </div>
            </div>

            @else
            <div class="text-center py-8">
                <i class="fas fa-laptop text-gray-300 text-5xl mb-4" aria-hidden="true"></i>
                <p class="text-gray-600 mb-2">Tidak ada perangkat tersedia</p>
                <p class="text-sm text-gray-500">Pastikan ada perangkat EduBraille yang aktif dan terhubung</p>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-end">
            <a href="{{ route('user.jadwal-belajar') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 text-center font-medium rounded-lg hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
               aria-label="Batal memilih perangkat">
                Batal
            </a>
            <button 
                type="submit" 
                id="submitBtn"
                class="px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
                aria-label="Kirim materi ke perangkat terpilih">
                <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                Kirim ke Perangkat
            </button>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-600 mr-3 mt-0.5" aria-hidden="true"></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Pilih minimal satu perangkat untuk melanjutkan</li>
                    <li>Materi akan dikirim ke semua perangkat yang dipilih secara bersamaan</li>
                    <li>Pastikan perangkat dalam status online untuk hasil terbaik</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Live Region for Screen Reader Announcements -->
<div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="devices[]"]');
    const selectAll = document.getElementById('selectAll');
    const submitBtn = document.getElementById('submitBtn');
    const announcements = document.getElementById('announcements');

    // Update button state based on selection
    function updateButtonState() {
        const checkedCount = document.querySelectorAll('input[name="devices[]"]:checked').length;
        submitBtn.disabled = checkedCount === 0;
        
        // Announce selection count
        if (checkedCount > 0) {
            announcements.textContent = `${checkedCount} perangkat dipilih`;
        }
    }

    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateButtonState();
            
            if (this.checked) {
                announcements.textContent = 'Semua perangkat dipilih';
            } else {
                announcements.textContent = 'Semua perangkat dibatalkan';
            }
        });
    }

    // Individual checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateButtonState();
            
            // Update select all state
            if (selectAll) {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                selectAll.checked = allChecked;
            }
        });
    });

    // Form submission
    const form = document.getElementById('deviceForm');
    form.addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('input[name="devices[]"]:checked').length;
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Pilih minimal satu perangkat');
            return;
        }

        announcements.textContent = 'Mengirim materi ke perangkat...';
    });

    // Initial state
    updateButtonState();
});
</script>
@endpush
@endsection