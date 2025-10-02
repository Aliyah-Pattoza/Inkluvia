<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LembagaController;
use App\Http\Controllers\UserMaterialController;
use App\Http\Controllers\PerpustakaanController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialRequestController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth routes - Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User routes
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/jadwal-belajar', [JadwalController::class, 'index'])->name('jadwal-belajar');
        Route::get('/jadwal-belajar/create', [JadwalController::class, 'create'])->name('jadwal-belajar.create');
        Route::post('/jadwal-belajar', [JadwalController::class, 'store'])->name('jadwal-belajar.store');
        Route::get('/jadwal-belajar/{jadwal}/edit', [JadwalController::class, 'edit'])->name('jadwal-belajar.edit');
        Route::put('/jadwal-belajar/{jadwal}', [JadwalController::class, 'update'])->name('jadwal-belajar.update');
        Route::delete('/jadwal-belajar/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal-belajar.destroy');
        Route::get('/jadwal-belajar/{jadwal}/start', [JadwalController::class, 'startSession'])->name('jadwal-belajar.start');
        Route::post('/jadwal-belajar/{jadwal}/send', [JadwalController::class, 'sendToDevices'])->name('jadwal-belajar.send');
        Route::get('/jadwal-belajar/{jadwal}/learn', [JadwalController::class, 'learn'])->name('jadwal-belajar.learn');
        
        // Materi Saya routes
        Route::get('/materi-saya', [UserMaterialController::class, 'index'])->name('materi-saya');
        Route::get('/materi-saya/create', [UserMaterialController::class, 'create'])->name('materi-saya.create');
        Route::post('/materi-saya', [UserMaterialController::class, 'store'])->name('materi-saya.store');
        Route::get('/materi-saya/{material}/edit', [UserMaterialController::class, 'edit'])->name('materi-saya.edit');
        Route::put('/materi-saya/{material}', [UserMaterialController::class, 'update'])->name('materi-saya.update');
        Route::delete('/materi-saya/{material}', [UserMaterialController::class, 'destroy'])->name('materi-saya.destroy');
        Route::get('/materi-saya/{material}/preview', [UserMaterialController::class, 'preview'])->name('materi-saya.preview');
        Route::get('/materi-saya/{material}/download', [UserMaterialController::class, 'download'])->name('materi-saya.download');

        // Perpustakaan routes
        Route::get('/perpustakaan', [PerpustakaanController::class, 'index'])->name('perpustakaan');
        Route::get('/materi-tersimpan', [PerpustakaanController::class, 'savedMaterials'])->name('materi-tersimpan');
        Route::post('/perpustakaan/{material}/toggle-saved', [PerpustakaanController::class, 'toggleSaved'])->name('perpustakaan.toggle-saved');
        Route::get('/perpustakaan/{material}/preview', [PerpustakaanController::class, 'preview'])->name('perpustakaan.preview');
        Route::get('/perpustakaan/{material}/preview-page', [PerpustakaanController::class, 'showPreview'])->name('perpustakaan.preview-page');
        Route::get('/perpustakaan/{material}/send', [PerpustakaanController::class, 'sendToDevice'])->name('perpustakaan.send');
    });
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('/manajemen-pengguna', [UserController::class, 'index'])->name('kelola-pengguna');
        Route::get('/manajemen-pengguna/create', [UserController::class, 'create'])->name('kelola-pengguna.create');
        Route::post('/manajemen-pengguna', [UserController::class, 'store'])->name('kelola-pengguna.store');
        Route::get('/manajemen-pengguna/{user}/edit', [UserController::class, 'edit'])->name('kelola-pengguna.edit');
        Route::put('/manajemen-pengguna/{user}', [UserController::class, 'update'])->name('kelola-pengguna.update');
        Route::delete('/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('kelola-pengguna.destroy');
        
        // Lembaga Management
        Route::get('/manajemen-lembaga', [LembagaController::class, 'index'])->name('manajemen-lembaga');
        Route::get('/manajemen-lembaga/create', [LembagaController::class, 'create'])->name('manajemen-lembaga.create');
        Route::post('/manajemen-lembaga', [LembagaController::class, 'store'])->name('manajemen-lembaga.store');
        Route::get('/manajemen-lembaga/{lembaga}/edit', [LembagaController::class, 'edit'])->name('manajemen-lembaga.edit');
        Route::put('/manajemen-lembaga/{lembaga}', [LembagaController::class, 'update'])->name('manajemen-lembaga.update');
        Route::delete('/manajemen-lembaga/{lembaga}', [LembagaController::class, 'destroy'])->name('manajemen-lembaga.destroy');
        
        // Material Management
        Route::get('/manajemen-materi', [MaterialController::class, 'index'])->name('manajemen-materi');
        Route::get('/manajemen-materi/create', [MaterialController::class, 'create'])->name('manajemen-materi.create');
        Route::post('/manajemen-materi', [MaterialController::class, 'store'])->name('manajemen-materi.store');
        Route::get('/manajemen-materi/{material}', [MaterialController::class, 'show'])->name('manajemen-materi.show');
        Route::get('/manajemen-materi/{material}/braille', [MaterialController::class, 'showWithBraille'])->name('manajemen-materi.braille');
        Route::get('/manajemen-materi/{material}/braille-content', [MaterialController::class, 'getBrailleContent'])->name('manajemen-materi.braille-content');
        Route::get('/manajemen-materi/{material}/edit', [MaterialController::class, 'edit'])->name('manajemen-materi.edit');
        Route::put('/manajemen-materi/{material}', [MaterialController::class, 'update'])->name('manajemen-materi.update');
        Route::delete('/manajemen-materi/{material}', [MaterialController::class, 'destroy'])->name('manajemen-materi.destroy');
        Route::post('/manajemen-materi/{material}/reconvert', [MaterialController::class, 'reconvert'])->name('manajemen-materi.reconvert');
        Route::get('/manajemen-materi/{material}/preview', [MaterialController::class, 'preview'])->name('manajemen-materi.preview');
        Route::get('/manajemen-materi/{material}/download-json', [MaterialController::class, 'downloadJson'])->name('manajemen-materi.download-json');
        Route::post('/manajemen-materi/{material}/update-status', [MaterialController::class, 'updateStatus'])->name('manajemen-materi.update-status');
        Route::post('/manajemen-materi/preview-conversion', [MaterialController::class, 'previewConversion'])->name('manajemen-materi.preview-conversion');
        Route::get('/manajemen-materi/test-conversion', [MaterialController::class, 'testConversion'])->name('manajemen-materi.test-conversion');
        
        // Material Request Management
        Route::get('/request-materi', [MaterialRequestController::class, 'index'])->name('request-materi');
        Route::get('/request-materi/statistics', [MaterialRequestController::class, 'statistics'])->name('request-materi.statistics');
        Route::get('/request-materi/{request}', [MaterialRequestController::class, 'show'])->name('request-materi.show');
        Route::post('/request-materi/{request}/approve', [MaterialRequestController::class, 'approve'])->name('request-materi.approve');
        Route::post('/request-materi/{request}/reject', [MaterialRequestController::class, 'reject'])->name('request-materi.reject');
        Route::post('/request-materi/{request}/in-progress', [MaterialRequestController::class, 'markInProgress'])->name('request-materi.in-progress');
        Route::post('/request-materi/{request}/complete', [MaterialRequestController::class, 'complete'])->name('request-materi.complete');
        
        // Device Management
        Route::get('/manajemen-perangkat', [DeviceController::class, 'index'])->name('kelola-perangkat');
        Route::get('/manajemen-perangkat/create', [DeviceController::class, 'create'])->name('kelola-perangkat.create');
        Route::post('/manajemen-perangkat', [DeviceController::class, 'store'])->name('kelola-perangkat.store');
        Route::get('/manajemen-perangkat/{device}/edit', [DeviceController::class, 'edit'])->name('kelola-perangkat.edit');
        Route::put('/manajemen-perangkat/{device}', [DeviceController::class, 'update'])->name('kelola-perangkat.update');
        Route::delete('/manajemen-perangkat/{device}', [DeviceController::class, 'destroy'])->name('kelola-perangkat.destroy');
        Route::post('/manajemen-perangkat/{device}/ping', [DeviceController::class, 'ping'])->name('kelola-perangkat.ping');
        Route::post('/manajemen-perangkat/{device}/status', [DeviceController::class, 'requestStatus'])->name('kelola-perangkat.status');
        Route::get('/manajemen-perangkat/users-by-lembaga', [DeviceController::class, 'getUsersByLembaga'])->name('kelola-perangkat.users-by-lembaga');
        
    });
});

// API routes untuk future development
Route::prefix('api')->group(function () {
    Route::get('/lembagas', function () {
        return response()->json(\App\Models\Lembaga::all());
    });
    
    // Temporary route to check access rights options
    Route::get('/check-access-rights', function () {
        $accessOptions = \App\Models\Material::getAksesOptions();
        $lembagas = \App\Models\Lembaga::all();
        
        return response()->json([
            'access_options' => $accessOptions,
            'lembagas' => $lembagas,
            'total_options' => count($accessOptions)
        ]);
    });
});