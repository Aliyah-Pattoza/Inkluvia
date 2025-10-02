@extends('layouts.user')

@section('title', 'Edit Jadwal')

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
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Edit Jadwal</h1>
        <p class="text-gray-600">Perbarui informasi jadwal sesi belajar</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST" action="{{ route('user.jadwal-belajar.update', $jadwal) }}">
            @csrf
            @method('PUT')
            
            <!-- Tanggal -->
            <div class="mb-6">
                <label for="tanggal" class="block text-base font-semibold text-gray-900 mb-3">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="tanggal" 
                       name="tanggal" 
                       required
                       min="{{ date('Y-m-d') }}"
                       class="w-full text-lg px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       aria-required="true"
                       value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}">
            </div>

            <!-- Waktu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="waktu_mulai" class="block text-base font-semibold text-gray-900 mb-3">
                        Waktu Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           id="waktu_mulai" 
                           name="waktu_mulai" 
                           required
                           class="w-full text-lg px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           aria-required="true"
                           value="{{ old('waktu_mulai', \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i')) }}">
                </div>
                
                <div>
                    <label for="waktu_selesai" class="block text-base font-semibold text-gray-900 mb-3">
                        Waktu Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           id="waktu_selesai" 
                           name="waktu_selesai" 
                           required
                           class="w-full text-lg px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           aria-required="true"
                           value="{{ old('waktu_selesai', \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i')) }}">
                </div>
            </div>

            <!-- Materi -->
            <div class="mb-6">
                <label class="block text-base font-semibold text-gray-900 mb-3">
                    Pilih Materi <span class="text-red-500">*</span>
                </label>
                
                @if($savedMaterials->count() > 0)
                <div class="space-y-3">
                    @foreach($savedMaterials as $material)
                    <div class="relative">
                        <input type="radio" 
                            id="material_{{ $material->id }}" 
                            name="material_id" 
                            value="{{ $material->id }}"
                            {{ old('material_id', $jadwal->getOriginalMaterialId()) == $material->id ? 'checked' : '' }}
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
            </div>

            <!-- Pengulangan -->
            <div class="mb-8">
                <label class="block text-base font-semibold text-gray-900 mb-3">
                    Pengulangan <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <div class="relative">
                        <input type="radio" 
                               id="pengulangan_tidak" 
                               name="pengulangan" 
                               value="tidak"
                               {{ old('pengulangan', $jadwal->pengulangan) == 'tidak' ? 'checked' : '' }}
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
                               {{ old('pengulangan', $jadwal->pengulangan) == 'harian' ? 'checked' : '' }}
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
                               {{ old('pengulangan', $jadwal->pengulangan) == 'mingguan' ? 'checked' : '' }}
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
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('user.jadwal-belajar') }}" 
                   class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 text-center font-semibold rounded-lg hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    <i class="fas fa-save mr-2" aria-hidden="true"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Live Region for Screen Reader -->
<div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Time validation
    const waktuMulai = document.getElementById('waktu_mulai');
    const waktuSelesai = document.getElementById('waktu_selesai');
    
    function validateTime() {
        if (waktuMulai.value && waktuSelesai.value && waktuMulai.value >= waktuSelesai.value) {
            waktuSelesai.setCustomValidity('Waktu selesai harus lebih dari waktu mulai');
        } else {
            waktuSelesai.setCustomValidity('');
        }
    }
    
    waktuMulai.addEventListener('change', validateTime);
    waktuSelesai.addEventListener('change', validateTime);
});
</script>
@endpush
@endsection