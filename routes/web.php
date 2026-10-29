<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinasiController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ManifesController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/paket/{paket}', [BerandaController::class, 'detail'])->name('paket.detail');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/jadwal/{jadwal}/pesan', [PemesananController::class, 'form'])->name('pemesanan.form');
    Route::post('/jadwal/{jadwal}/pesan', [PemesananController::class, 'store'])->name('pemesanan.store');

    Route::get('/pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::get('/pemesanan/{pemesanan}', [PemesananController::class, 'show'])->name('pemesanan.show');
    Route::get('/pemesanan/{pemesanan}/pembayaran', [PemesananController::class, 'pembayaran'])->name('pemesanan.pembayaran');
    Route::post('/pemesanan/{pemesanan}/bayar', [PemesananController::class, 'unggahPembayaran'])->name('pemesanan.bayar');
    Route::post('/pemesanan/{pemesanan}/batal', [PemesananController::class, 'batal'])->name('pemesanan.batal');
    Route::get('/pemesanan/{pemesanan}/voucher', [PemesananController::class, 'lihatVoucher'])->name('pemesanan.voucher');
    Route::get('/pemesanan/{pemesanan}/voucher/unduh', [PemesananController::class, 'unduhVoucher'])->name('pemesanan.voucher.unduh');
    Route::get('/pemesanan/{pemesanan}/berkas/{jenis}/{id}', [PemesananController::class, 'berkas'])->name('pemesanan.berkas');

    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('destinasi', DestinasiController::class)->except('show');
    Route::resource('paket', PaketController::class)->except('show');
    Route::resource('jadwal', JadwalController::class)->except('show');

    Route::get('verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::get('verifikasi/{pemesanan}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
    Route::post('verifikasi/{pemesanan}/setujui', [VerifikasiController::class, 'setujui'])->name('verifikasi.setujui');
    Route::post('verifikasi/{pemesanan}/tolak', [VerifikasiController::class, 'tolak'])->name('verifikasi.tolak');

    Route::get('manifes', [ManifesController::class, 'index'])->name('manifes.index');
    Route::get('manifes/{jadwal}', [ManifesController::class, 'show'])->name('manifes.show');
    Route::get('manifes/{jadwal}/pdf', [ManifesController::class, 'pdf'])->name('manifes.pdf');

    Route::get('voucher', [VoucherController::class, 'index'])->name('voucher.index');
    Route::post('voucher', [VoucherController::class, 'validasi'])->name('voucher.validasi');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
    Route::get('laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
});
