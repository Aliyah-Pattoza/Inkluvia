@extends('layouts.user')

@section('title', 'Buat Jadwal Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('user.jadwal-belajar') }}" 
           class="inline-flex items-center text-primary hover:text-primary-dark font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded px-2 py-1"
           aria-label="Kembali ke daftar jadwal">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
            Kembali ke Jadwal
        </a>
    </div>

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Buat Jadwal Baru</h1>
        <p class="text-gray-600">Isi informasi di bawah untuk membuat jadwal sesi belajar</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST" action="{{ route('user.jadwal-belajar.store') }}" id="createJadwalForm">
            @csrf
            
            <!-- Step Indicator -->
            <div class="mb-8" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="4">
                <div class="flex justify-between items-center relative">
                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 -z-10"></div>
                    <div class="step-item active">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">1</div>
                        <span class="text-xs mt-2 text-gray-700 font-medium">Tanggal</span>
                    </div>
                    <div class="step-item">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">2</div>
                        <span class="text-xs mt-2 text-gray-500">Waktu</span>
                    </div>
                    <div class="step-item">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">3</div>
                        <span class="text-xs mt-2 text-gray-500">Materi</span>
                    </div>
                    <div class="step-item">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">4</div>
                        <span class="text-xs mt-2 text-gray-500">Pengulangan</span>
                    </div>
                </div>
            </div>

            <!-- Step 1: Tanggal -->
            <div class="form-step active" data-step="1">
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-900 mb-4">1. Pilih Tanggal</legend>
                    <div class="space-y-4">
                        <div>
                            <label for="tanggal" class="block text-base font-medium text-gray-900 mb-3">
                                Kapan jadwal ini dilaksanakan?
                            </label>
                            <input type="date" 
                                   id="tanggal" 
                                   name="tanggal" 
                                   required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full text-lg px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   aria-required="true"
                                   aria-describedby="tanggal-help"
                                   value="{{ old('tanggal') }}">
                            <p id="tanggal-help" class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                                Pilih tanggal untuk sesi belajar
                            </p>
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- Step 2: Waktu -->
            <div class="form-step" data-step="2">
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-900 mb-4">2. Tentukan Waktu</legend>
                    <div class="space-y-4">
                        <div>
                            <label for="waktu_mulai" class="block text-base font-medium text-gray-900 mb-3">
                                Jam Mulai
                            </label>
                            <input type="time" 
                                   id="waktu_mulai" 
                                   name="waktu_mulai" 
                                   required
                                   class="w-full text-lg px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   aria-required="true"
                                   value="{{ old('waktu_mulai', '08:00') }}">
                        </div>
                        
                        <div>
                            <label for="waktu_selesai" class="block text-base font-medium text-gray-900 mb-3">
                                Jam Selesai
                            </label>
                            <input type="time" 
                                   id="waktu_selesai" 
                                   name="waktu_selesai" 
                                   required
                                   class="w-full text-lg px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   aria-required="true"
                                   value="{{ old('waktu_selesai', '09:00') }}">
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- Step 3: Materi -->
            <div class="form-step" data-step="3">
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-900 mb-4">3. Pilih Materi</legend>
                    
                    @if($savedMaterials->count() > 0)
                    <div class="space-y-3">
                        @foreach($savedMaterials as $material)
                        <div class="relative">
                            <input type="radio" 
                                id="material_{{ $material->id }}" 
                                name="material_id" 
                                value="{{ $material->id }}"
                                {{ old('material_id') == $material->id ? 'checked' : ($loop->first ? 'checked' : '') }}
                                class="peer sr-only"
                                required>
                            <label for="material_{{ $material->id }}" 
                                class="flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                <i class="fas fa-book text-primary mr-4 text-xl mt-1" aria-hidden="true"></i>
                                <div class="flex-1">
                                    <div class="text-base font-medium text-gray-900 mb-1">{{ $material->judul }}</div>
                                    <div class="text-sm text-gray-600 mb-2">
                                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full mr-2">
                                            {{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? $material->tingkat }}
                                        </span>
                                        @if($material->kategori)
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full mr-2">
                                            {{ \App\Models\Material::getKategoriOptions()[$material->kategori] ?? $material->kategori }}
                                        </span>
                                        @endif
                                        <span class="text-gray-500">{{ $material->total_halaman }} halaman</span>
                                    </div>
                                    @if($material->deskripsi)
                                    <p class="text-sm text-gray-500 leading-relaxed">
                                        {{ Str::limit($material->deskripsi, 100) }}
                                    </p>
                                    @endif
                                </div>
                                <i class="fas fa-check-circle text-primary ml-4 opacity-0 peer-checked:opacity-100 transition-opacity text-xl" aria-hidden="true"></i>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                        <i class="fas fa-bookmark text-4xl text-gray-300 mb-4" aria-hidden="true"></i>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Materi Tersimpan</h3>
                        <p class="text-gray-500 mb-4">
                            Anda perlu menyimpan materi terlebih dahulu dari perpustakaan untuk membuat jadwal belajar.
                        </p>
                        <a href="{{ route('user.perpustakaan') }}" 
                        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary transition-colors duration-200">
                            <i class="fas fa-book mr-2" aria-hidden="true"></i>
                            Jelajahi Perpustakaan
                        </a>
                    </div>
                    @endif
                </fieldset>
            </div>

            <!-- Step 4: Pengulangan -->
            <div class="form-step" data-step="4">
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-900 mb-4">4. Pengulangan</legend>
                    <div class="space-y-3">
                        <div class="relative">
                            <input type="radio" 
                                   id="pengulangan_tidak" 
                                   name="pengulangan" 
                                   value="tidak"
                                   {{ old('pengulangan', 'tidak') == 'tidak' ? 'checked' : '' }}
                                   class="peer sr-only"
                                   required>
                            <label for="pengulangan_tidak" 
                                   class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                <i class="fas fa-calendar-day text-primary mr-4 text-xl" aria-hidden="true"></i>
                                <div class="flex-1">
                                    <div class="text-base font-medium text-gray-900">Tidak Berulang</div>
                                    <div class="text-sm text-gray-500">Hanya sekali sesi</div>
                                </div>
                                <i class="fas fa-check-circle text-primary ml-auto opacity-0 peer-checked:opacity-100 transition-opacity" aria-hidden="true"></i>
                            </label>
                        </div>

                        <div class="relative">
                            <input type="radio" 
                                   id="pengulangan_harian" 
                                   name="pengulangan" 
                                   value="harian"
                                   {{ old('pengulangan') == 'harian' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <label for="pengulangan_harian" 
                                   class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                <i class="fas fa-redo text-primary mr-4 text-xl" aria-hidden="true"></i>
                                <div class="flex-1">
                                    <div class="text-base font-medium text-gray-900">Harian</div>
                                    <div class="text-sm text-gray-500">Setiap hari di waktu yang sama</div>
                                </div>
                                <i class="fas fa-check-circle text-primary ml-auto opacity-0 peer-checked:opacity-100 transition-opacity" aria-hidden="true"></i>
                            </label>
                        </div>

                        <div class="relative">
                            <input type="radio" 
                                   id="pengulangan_mingguan" 
                                   name="pengulangan" 
                                   value="mingguan"
                                   {{ old('pengulangan') == 'mingguan' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <label for="pengulangan_mingguan" 
                                   class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                <i class="fas fa-calendar-week text-primary mr-4 text-xl" aria-hidden="true"></i>
                                <div class="flex-1">
                                    <div class="text-base font-medium text-gray-900">Mingguan</div>
                                    <div class="text-sm text-gray-500">Setiap minggu di hari dan waktu yang sama</div>
                                </div>
                                <i class="fas fa-check-circle text-primary ml-auto opacity-0 peer-checked:opacity-100 transition-opacity" aria-hidden="true"></i>
                            </label>
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                <button type="button" 
                        id="prevBtn"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                    Sebelumnya
                </button>
                
                <button type="button" 
                        id="nextBtn"
                        class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    Selanjutnya
                    <i class="fas fa-arrow-right ml-2" aria-hidden="true"></i>
                </button>
                
                <button type="submit" 
                        id="submitBtn"
                        class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors focus:ring-2 focus:ring-green-500 focus:ring-offset-2 hidden">
                    <i class="fas fa-check mr-2" aria-hidden="true"></i>
                    Buat Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Live Region for Screen Reader -->
<div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

@push('styles')
<style>
.form-step {
    display: none;
}
.form-step.active {
    display: block;
}
.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;
    const form = document.getElementById('createJadwalForm');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const announcements = document.getElementById('announcements');

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
        
        // Show current step
        const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        if (currentStepEl) {
            currentStepEl.classList.add('active');
            
            // Focus first input in step
            const firstInput = currentStepEl.querySelector('input, select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        }

        // Update progress indicator
        document.querySelectorAll('.step-item').forEach((item, index) => {
            const circle = item.querySelector('div');
            const text = item.querySelector('span');
            
            if (index + 1 < step) {
                circle.classList.remove('bg-gray-200', 'text-gray-500');
                circle.classList.add('bg-green-600', 'text-white');
                text.classList.remove('text-gray-500');
                text.classList.add('text-gray-700');
            } else if (index + 1 === step) {
                circle.classList.remove('bg-gray-200', 'bg-green-600', 'text-gray-500');
                circle.classList.add('bg-primary', 'text-white');
                text.classList.remove('text-gray-500');
                text.classList.add('text-gray-700');
            } else {
                circle.classList.remove('bg-primary', 'bg-green-600', 'text-white');
                circle.classList.add('bg-gray-200', 'text-gray-500');
                text.classList.remove('text-gray-700');
                text.classList.add('text-gray-500');
            }
        });

        // Update buttons
        prevBtn.disabled = step === 1;
        
        if (step === totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }

        // Update progressbar
        const progressbar = document.querySelector('[role="progressbar"]');
        progressbar.setAttribute('aria-valuenow', step);

        // Announce step change
        const stepName = currentStepEl.querySelector('legend').textContent;
        announcements.textContent = `Langkah ${step} dari ${totalSteps}: ${stepName}`;
    }

    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });

    function validateStep(step) {
        const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = stepEl.querySelectorAll('input[required], select[required]');
        
        for (let input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                return false;
            }
        }
        
        // Additional validation for time
        if (step === 2) {
            const waktuMulai = document.getElementById('waktu_mulai').value;
            const waktuSelesai = document.getElementById('waktu_selesai').value;
            
            if (waktuMulai && waktuSelesai && waktuMulai >= waktuSelesai) {
                alert('Waktu selesai harus lebih dari waktu mulai');
                return false;
            }
        }
        
        return true;
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.type !== 'submit') {
            e.preventDefault();
            if (currentStep < totalSteps) {
                nextBtn.click();
            }
        }
    });

    // Initialize
    showStep(currentStep);
    
    // Announce page load
    setTimeout(() => {
        announcements.textContent = 'Formulir buat jadwal. Gunakan tombol navigasi atau Enter untuk melanjutkan ke langkah berikutnya.';
    }, 500);
});
</script>
@endpush
@endsection