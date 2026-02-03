<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HALAMAN PUBLIK (TANPA AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| AREA YANG BUTUH LOGIN & VERIFIKASI EMAIL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // DASHBOARD UTAMA
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // DASHBOARD ADMIN
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->middleware('role:admin');

    // DASHBOARD PIMPINAN
    Route::get('/pimpinan', function () {
        return view('pimpinan.dashboard');
    })->middleware('role:pimpinan');

    /*
    |--------------------------------------------------------------------------
    | PROFIL USER (AUTH DEFAULT)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | HALAMAN MANAJEMEN SURAT (GABUNG SURAT MASUK + KELUAR)
    |--------------------------------------------------------------------------
    */
    Route::get('/surat', [SuratController::class, 'surat'])
        ->name('surat');

    /*
    |--------------------------------------------------------------------------
    | SURAT MASUK (CRUD LENGKAP)
    |--------------------------------------------------------------------------
    */
    Route::resource('surat-masuk', SuratMasukController::class)
        ->except(['index', 'show'])
        ->middleware('can.modify')
        ->names([
            'create'  => 'surat-masuk.create',
            'store'   => 'surat-masuk.store',
            'edit'    => 'surat-masuk.edit',
            'update'  => 'surat-masuk.update',
            'destroy' => 'surat-masuk.destroy',
        ]);
    
    // Allow index and show for all authenticated users
    Route::resource('surat-masuk', SuratMasukController::class)
        ->only(['index', 'show'])
        ->names([
            'index'   => 'surat-masuk.index',
            'show'    => 'surat-masuk.show',
        ]);

    // DOWNLOAD FILE PDF SURAT MASUK (allow for all)
    Route::get('/surat-masuk/{suratMasuk}/download', [SuratMasukController::class, 'download'])
        ->name('surat-masuk.download');

    /*
    |--------------------------------------------------------------------------
    | SURAT KELUAR / BALASAN (CRUD LENGKAP)
    |--------------------------------------------------------------------------
    */
    Route::resource('surat-keluar', SuratKeluarController::class)
        ->except(['index', 'show'])
        ->middleware('can.modify')
        ->names([
            'create'  => 'surat-keluar.create',
            'store'   => 'surat-keluar.store',
            'edit'    => 'surat-keluar.edit',
            'update'  => 'surat-keluar.update',
            'destroy' => 'surat-keluar.destroy',
        ]);
    
    // Allow index and show for all authenticated users
    Route::resource('surat-keluar', SuratKeluarController::class)
        ->only(['index', 'show'])
        ->names([
            'index'   => 'surat-keluar.index',
            'show'    => 'surat-keluar.show',
        ]);

    // DOWNLOAD FILE PDF BALASAN (allow for all)
    Route::get('/surat-keluar/{surat_keluar}/download', [SuratKeluarController::class, 'download'])
        ->name('surat-keluar.download');

    // HAPUS BALASAN (protect with middleware)
    Route::delete('/surat-keluar/{surat}/balasan', [SuratController::class, 'destroyBalasan'])
        ->middleware('can.modify')
        ->name('surat-keluar.balasan.destroy');

    /*
    |--------------------------------------------------------------------------
    | SURAT RESOURCE (BACKUP / LEGACY)
    |--------------------------------------------------------------------------
    */
    Route::resource('surat-resource', SuratController::class)
        ->except(['index', 'show'])
        ->middleware('can.modify')
        ->names([
            'create'  => 'surat-resource.create',
            'store'   => 'surat-resource.store',
            'edit'    => 'surat-resource.edit',
            'update'  => 'surat-resource.update',
            'destroy' => 'surat-resource.destroy',
        ]);
    
    // Allow index and show for all authenticated users
    Route::resource('surat-resource', SuratController::class)
        ->only(['index', 'show'])
        ->names([
            'index'   => 'surat-resource.index',
            'show'    => 'surat-resource.show',
        ]);

    /*
    |--------------------------------------------------------------------------
    | LAPORAN SURAT - FULL CRUD + EXPORT ✅
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export/pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
    });

    /*
    |--------------------------------------------------------------------------
    | PROFIL UMUM (DATA INSTANSI)
    |--------------------------------------------------------------------------
    */
    Route::get('/profil', [ProfilController::class, 'index'])
        ->name('profil');

    Route::post('/profil', [ProfilController::class, 'update'])
        ->middleware('can.modify')
        ->name('profil.update');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
