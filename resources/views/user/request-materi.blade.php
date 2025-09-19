@extends('layouts.app')

@section('title', 'Request Materi')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="h3 mb-3 text-gray-800">Request Materi Baru</h1>
                <p class="text-muted">Ajukan permintaan materi baru yang Anda butuhkan</p>
            </div>

            <!-- Form -->
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('user.request-materi.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="judul_materi" class="form-label">Judul Materi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul_materi') is-invalid @enderror" 
                                   id="judul_materi" name="judul_materi" value="{{ old('judul_materi') }}" 
                                   placeholder="Contoh: Matematika Dasar Kelas 1 SD" required>
                            @error('judul_materi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Materi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="4" 
                                      placeholder="Jelaskan detail materi yang Anda butuhkan..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control @error('kategori') is-invalid @enderror" 
                                            id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach(\App\Models\Material::getKategoriOptions() as $key => $value)
                                            <option value="{{ $key }}" {{ old('kategori') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                                    <select class="form-control @error('tingkat') is-invalid @enderror" 
                                            id="tingkat" name="tingkat" required>
                                        <option value="">Pilih Tingkat</option>
                                        @foreach(\App\Models\Material::getTingkatOptions() as $key => $value)
                                            <option value="{{ $key }}" {{ old('tingkat') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tingkat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="prioritas" class="form-label">Prioritas <span class="text-danger">*</span></label>
                            <select class="form-control @error('prioritas') is-invalid @enderror" 
                                    id="prioritas" name="prioritas" required>
                                <option value="">Pilih Prioritas</option>
                                @foreach(\App\Models\MaterialRequest::getPrioritasOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('prioritas') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <small class="text-muted">
                                    • <strong>Rendah:</strong> Tidak mendesak<br>
                                    • <strong>Sedang:</strong> Diperlukan dalam waktu dekat<br>
                                    • <strong>Tinggi:</strong> Sangat mendesak
                                </small>
                            </div>
                            @error('prioritas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.my-requests') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-list"></i> Lihat Request Saya
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-primary"></i> Informasi
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Proses Request:</h6>
                            <ol>
                                <li>Kirim request materi</li>
                                <li>Admin meninjau request</li>
                                <li>Admin approve/reject</li>
                                <li>Materi dibuat jika disetujui</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6>Tips Request yang Baik:</h6>
                            <ul>
                                <li>Berikan judul yang jelas</li>
                                <li>Deskripsikan kebutuhan secara detail</li>
                                <li>Tentukan prioritas dengan tepat</li>
                                <li>Pilih kategori dan tingkat yang sesuai</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
