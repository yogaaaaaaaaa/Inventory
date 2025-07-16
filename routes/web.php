<?php

use App\Models\Tanaman;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\KelurahanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AuthController;
use App\Models\Kelurahan;
use Illuminate\Support\Facades\Auth;

// Route default /
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('dashboard');

Route::get('/barang-masuk', [ItemController::class, 'barangMasuk'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('items.barangMasuk');
Route::put('/barang-masuk/{item}', [ItemController::class, 'updateProduksi'])->middleware('auth', 'role:admin')->name('items.updateProduksi');
Route::post('/categories', [CategoryController::class, 'store'])->middleware('auth', 'role:admin')->name('categories.store');


Route::get('/tanaman', [ItemController::class, 'tanaman'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('items.tanaman');
Route::put('/tanaman/{item}/mati', [ItemController::class, 'updateMati'])->middleware('auth', 'role:admin')->name('items.updateMati');
Route::put('/hewan/{item}/mati', [ItemController::class, 'updateMati'])->middleware('auth', 'role:admin')->name('items.updateMati');
Route::get('/hewan', [ItemController::class, 'hewan'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('items.hewan');
//Route::put('/items/{id}/update-mati', [ItemController::class, 'updateMati'])->name('items.updateMati');
Route::post('/items', [ItemController::class, 'store'])->middleware('auth', 'role:admin')->name('items.store');
Route::delete('/items/{id}', [ItemController::class, 'destroy'])->middleware('auth', 'role:admin')->name('items.destroy');


Route::get('/barang-keluar', [DistribusiController::class, 'index'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('items.barangkeluar');
Route::post('/barang-keluar', [DistribusiController::class, 'store'])->middleware('auth', 'role:admin')->name('distribusi.store');
Route::get('/kelurahan-by-kecamatan/{kecamatan_id}', [DistribusiController::class, 'getKelurahan'])->middleware('auth', 'role:admin');
Route::get('/getKelurahan/{kecamatan_id}', [DistribusiController::class, 'getKelurahan'])->middleware('auth', 'role:admin');
Route::get('/items-by-kategori/{kategori}', [DistribusiController::class, 'getItemsByKategori'])->middleware('auth', 'role:admin');
Route::put('/distribusi/{id}', [DistribusiController::class, 'update'])->middleware('auth', 'role:admin')->name('distribusi.update');
Route::delete('/distribusi/{id}', [DistribusiController::class, 'destroy'])->middleware('auth', 'role:admin')->name('distribusi.destroy');



Route::get('/export', [ExportController::class, 'index'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('export.index');
Route::get('/export/excel', [ExportController::class, 'exportProduksi'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('export.produksi');
Route::get('/export/distribusi', [ExportController::class, 'exportDistribusi'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('export.distribusi');
Route::post('/kecamatan/store', [KecamatanController::class, 'store'])->middleware('auth', 'role:admin')->name('kecamatan.store');
Route::post('/kelurahan/store', [KelurahanController::class, 'store'])->middleware('auth', 'role:admin')->name('kelurahan.store');

Route::get('/wilayah', [KelurahanController::class, 'kelurahanIndex'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('wilayah.index');
Route::post('/kelurahan', [KelurahanController::class, 'store'])->middleware('auth', 'role:admin')->name('kelurahan.store');
Route::put('/kelurahan/{id}', [KelurahanController::class, 'update'])->middleware('auth', 'role:admin')->name('kelurahan.update');
Route::delete('/kelurahan/{id}', [KelurahanController::class, 'destroy'])->middleware('auth', 'role:admin')->name('kelurahan.destroy');




Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth', 'role:admin,kepala_upt,kepala_dinas')->name('logout');
