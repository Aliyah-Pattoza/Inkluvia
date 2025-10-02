@extends('layouts.app')

@section('title', 'Jadwal Belajar')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="h3 mb-3 text-gray-800">Jadwal Belajar</h1>
                <p class="text-muted">Kelola jadwal pembelajaran Anda</p>
            </div>

            <!-- Coming Soon Card -->
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-calendar-alt fa-4x text-primary"></i>
                    </div>
                    <h4 class="text-gray-800 mb-3">Fitur Jadwal Belajar</h4>
                    <p class="text-muted mb-4">
                        Fitur jadwal belajar akan segera hadir untuk membantu Anda mengatur waktu pembelajaran 
                        dan melacak progres belajar.
                    </p>
                    <div class="mt-4">
                        <span class="badge bg-warning text-dark">Coming Soon</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-book-open fa-2x text-primary mb-3"></i>
                            <h5>Mulai Belajar</h5>
                            <p class="text-muted">Akses materi pembelajaran yang tersedia</p>
                            <a href="{{ route('user.perpustakaan') }}" class="btn btn-primary">
                                Buka Perpustakaan
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-file-circle-plus fa-2x text-success mb-3"></i>
                            <h5>Request Materi</h5>
                            <p class="text-muted">Ajukan materi baru yang Anda butuhkan</p>
                            <a href="{{ route('user.request-materi') }}" class="btn btn-success">
                                Buat Request
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

