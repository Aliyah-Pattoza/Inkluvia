@extends('layouts.user')

@section('title', 'Jadwal Sesi Belajar')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Jadwal Sesi Belajar</h1>
            <p class="text-gray-600">Kelola jadwal pembelajaran Anda</p>
        </div>
        <a href="{{ route('user.jadwal-belajar.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
           aria-label="Buat jadwal baru">
            <i class="fas fa-plus mr-2" aria-hidden="true"></i>
            Buat Jadwal Baru
        </a>
    </div>

    <!-- Filter Jadwal -->
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter Jadwal</h2>
        <form method="GET" action="{{ route('user.jadwal-belajar') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="filter_tanggal" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <select id="filter_tanggal" name="filter_tanggal" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        aria-label="Filter berdasarkan tanggal">
                    <option value="hari_ini" {{ request('filter_tanggal') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="semua" {{ request('filter_tanggal') == 'semua' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>

            <div>
                <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="filter_status" name="filter_status" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        aria-label="Filter berdasarkan status">
                    <option value="">Semua Status</option>
                    <option value="belum_mulai" {{ request('filter_status') == 'belum_mulai' ? 'selected' : '' }}>Belum Mulai</option>
                    <option value="sedang_berlangsung" {{ request('filter_status') == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="selesai" {{ request('filter_status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="w-full px-6 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    <i class="fas fa-filter mr-2" aria-hidden="true"></i>
                    Terapkan Filter
                </button>
            </div>

            <div class="flex items-end">
                <a href="{{ route('user.jadwal-belajar') }}" 
                   class="w-full px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 text-center">
                    <i class="fas fa-redo mr-2" aria-hidden="true"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Jadwal List -->
    <div class="space-y-4">
        @forelse($jadwals as $jadwal)
        <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <!-- Date Badge -->
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-3"
                             style="background-color: {{ $jadwal->isToday() ? '#DBEAFE' : '#F3F4F6' }}; color: {{ $jadwal->isToday() ? '#1E40AF' : '#374151' }}">
                            <i class="far fa-calendar mr-2" aria-hidden="true"></i>
                            <span>{{ $jadwal->isToday() ? 'Hari Ini' : $jadwal->tanggal->format('d M Y') }}</span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $jadwal->judul }}</h3>

                        <!-- Time -->
                        <div class="flex items-center text-gray-600 mb-2">
                            <i class="far fa-clock mr-2" aria-hidden="true"></i>
                            <time datetime="{{ $jadwal->waktu_mulai }}">
                                {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}
                            </time>
                            <span class="mx-2">-</span>
                            <time datetime="{{ $jadwal->waktu_selesai }}">
                                {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                            </time>
                        </div>

                        <!-- Materi -->
                        @if($jadwal->materi)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-book mr-2" aria-hidden="true"></i>
                            <span>{{ $jadwal->materi }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col space-y-2 md:ml-4 md:min-w-[160px]">
                        <!-- Status Badge -->
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-medium"
                              style="background-color: {{ $jadwal->status === 'sedang_berlangsung' ? '#D1FAE5' : ($jadwal->status === 'selesai' ? '#F3F4F6' : '#DBEAFE') }}; 
                                     color: {{ $jadwal->status === 'sedang_berlangsung' ? '#065F46' : ($jadwal->status === 'selesai' ? '#6B7280' : '#1E40AF') }}">
                            {{ $jadwal->status_label }}
                        </span>

                        @if($jadwal->canStart())
                        <a href="{{ route('user.jadwal-belajar.start', $jadwal) }}" 
                           class="px-4 py-2 bg-primary text-white text-center font-semibold rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
                           aria-label="Mulai sesi {{ $jadwal->judul }}">
                            Mulai Sesi
                        </a>

                        <a href="{{ route('user.jadwal-belajar.edit', $jadwal) }}" 
                           class="px-4 py-2 bg-white border-2 border-primary text-primary text-center font-semibold rounded-lg hover:bg-primary hover:text-white transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
                           aria-label="Edit jadwal {{ $jadwal->judul }}">
                            <i class="fas fa-edit mr-1" aria-hidden="true"></i>
                            Edit
                        </a>

                        <form method="POST" action="{{ route('user.jadwal-belajar.destroy', $jadwal) }}" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')"
                              class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-white border-2 border-red-500 text-red-600 font-semibold rounded-lg hover:bg-red-600 hover:text-white transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    aria-label="Hapus jadwal {{ $jadwal->judul }}">
                                <i class="fas fa-trash mr-1" aria-hidden="true"></i>
                                Hapus
                            </button>
                        </form>
                        @elseif($jadwal->status === 'selesai')
                        <a href="{{ route('user.jadwal-belajar.edit', $jadwal) }}" 
                           class="px-4 py-2 bg-white border-2 border-gray-300 text-gray-600 text-center font-semibold rounded-lg hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                           aria-label="Edit jadwal {{ $jadwal->judul }}">
                            <i class="fas fa-edit mr-1" aria-hidden="true"></i>
                            Edit
                        </a>

                        <form method="POST" action="{{ route('user.jadwal-belajar.destroy', $jadwal) }}" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')"
                              class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-white border-2 border-red-500 text-red-600 font-semibold rounded-lg hover:bg-red-600 hover:text-white transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    aria-label="Hapus jadwal {{ $jadwal->judul }}">
                                <i class="fas fa-trash mr-1" aria-hidden="true"></i>
                                Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </article>
        @empty
        <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-200">
            <i class="far fa-calendar-times text-gray-300 text-6xl mb-4" aria-hidden="true"></i>
            <p class="text-gray-600 text-lg mb-2">Belum ada jadwal</p>
            <p class="text-sm text-gray-500 mb-4">Buat jadwal baru untuk memulai sesi belajar</p>
            <a href="{{ route('user.jadwal-belajar.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                Buat Jadwal Pertama
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Live Region for Screen Reader Announcements -->
<div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>
@endsection