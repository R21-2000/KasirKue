<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

// Rute Autentikasi
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);


Route::middleware('auth')->group(function () {
    // ... rute yang sudah ada ...
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Rute untuk Edit Profil Pengguna
    Route::get('/profile', [AuthController::class, 'showProfileForm'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    // [TAMBAHKAN INI] Rute untuk Menghapus Akun Sendiri
    Route::delete('/profile', [AuthController::class, 'destroyProfile'])->name('profile.destroy');


    Route::get('/', function () {
        return redirect()->route('dashboard.index');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::resource('produk', ProdukController::class)->except(['index', 'create', 'store']);

    Route::resource('satuan', SatuanController::class);

    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/stok/masuk', [StokController::class, 'masuk'])->name('stok.masuk');
    Route::post('/stok', [StokController::class, 'store'])->name('stok.store');
    Route::put('/stok/{id}', [StokController::class, 'update'])->name('stok.update');
    Route::delete('/stok/{id}', [StokController::class, 'destroy'])->name('stok.destroy');
    Route::get('/stok/list', [StokController::class, 'list'])->name('stok.list');


    Route::get('/kasir', [PenjualanController::class, 'kasir'])->name('kasir');
    Route::post('/kasir', [PenjualanController::class, 'store'])->name('kasir.store');
    Route::get('/api/produk', [ProdukController::class, 'searchApi'])->name('produk.searchApi');

    Route::get('/laporan', [PenjualanController::class, 'laporan'])->name('laporan');
    Route::get('/laporan-transaksi', [PenjualanController::class, 'laporanTransaksi'])->name('laporan.transaksi');
    Route::get('/laporan/filter', [PenjualanController::class, 'filterLaporan'])->name('laporan.filter');
    Route::get('/laporan/ekspor', [PenjualanController::class, 'eksporPdf'])->name('laporan.ekspor');

    Route::get('/daftar-stok', [StokController::class, 'daftarStok'])->name('stok.daftar');

    Route::get('/opname-stok', [StokController::class, 'opname'])->name('stok.opname');
    Route::post('/opname-stok', [StokController::class, 'storeOpname'])->name('stok.storeOpname');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

});
