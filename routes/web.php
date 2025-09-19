<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LembagaController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialRequestController;
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
        Route::get('/jadwal-belajar', function () {
            return view('user.jadwal-belajar');
        })->name('jadwal-belajar');
        
        // Material request routes
        Route::get('/request-materi', function () {
            return view('user.request-materi');
        })->name('request-materi');
        Route::post('/request-materi', [MaterialController::class, 'requestMaterial'])->name('request-materi.store');
        Route::get('/my-requests', [MaterialController::class, 'myRequests'])->name('my-requests');
        
        // Material library
        Route::get('/perpustakaan', [MaterialController::class, 'library'])->name('perpustakaan');
        Route::get('/materials/{material}/preview', [MaterialController::class, 'preview'])->name('materials.preview');
        Route::get('/materials/{material}/download', [MaterialController::class, 'download'])->name('materials.download');
        Route::get('/materials/{material}/download-braille', [MaterialController::class, 'downloadBraille'])->name('materials.download-braille');
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
        Route::get('/manajemen-perangkat', function () {
            return view('admin.manajemen-perangkat');
        })->name('kelola-perangkat');
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