<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KelolaKasirController;

// Rute Autentikasi
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    // Profil & Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfileForm'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [AuthController::class, 'destroyProfile'])->name('profile.destroy');

    // === RUTE YANG BISA DIAKSES KASIR & ADMIN ===
    Route::get('/', function () {
        // Jika kasir login, arahkan langsung ke halaman kasir. Jika admin, ke dashboard.
        // Menggunakan Auth::user() agar Intelephense tidak memberikan peringatan error merah
        return Auth::user()->role === 'admin'
            ? redirect()->route('dashboard.index')
            : redirect()->route('kasir');
    });

    Route::get('/kasir', [PenjualanController::class, 'kasir'])->name('kasir');
    Route::post('/kasir', [PenjualanController::class, 'store'])->name('kasir.store');
    Route::get('/api/produk', [ProdukController::class, 'searchApi'])->name('produk.searchApi');

    // === RUTE KHUSUS ADMIN ===
    Route::middleware(['role:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Kelola Kasir
        Route::resource('kelola-kasir', KelolaKasirController::class);

        // Produk (PENTING: Urutan rute custom harus di atas Route::resource)
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::resource('produk', ProdukController::class)->except(['index', 'create', 'store']);

        // Satuan
        Route::resource('satuan', SatuanController::class);

        // Stok
        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/stok/masuk', [StokController::class, 'masuk'])->name('stok.masuk');
        Route::post('/stok', [StokController::class, 'store'])->name('stok.store');
        Route::put('/stok/{id}', [StokController::class, 'update'])->name('stok.update');
        Route::delete('/stok/{id}', [StokController::class, 'destroy'])->name('stok.destroy');
        Route::get('/stok/list', [StokController::class, 'list'])->name('stok.list');
        Route::get('/daftar-stok', [StokController::class, 'daftarStok'])->name('stok.daftar');
        Route::get('/opname-stok', [StokController::class, 'opname'])->name('stok.opname');
        Route::post('/opname-stok', [StokController::class, 'storeOpname'])->name('stok.storeOpname');

        // Laporan
        Route::get('/laporan', [PenjualanController::class, 'laporan'])->name('laporan');
        Route::get('/laporan-transaksi', [PenjualanController::class, 'laporanTransaksi'])->name('laporan.transaksi');
        Route::get('/laporan/filter', [PenjualanController::class, 'filterLaporan'])->name('laporan.filter');
        Route::get('/laporan/ekspor', [PenjualanController::class, 'eksporPdf'])->name('laporan.ekspor');
    });
});
