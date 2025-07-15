<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CabangLokasiController;
use App\Http\Controllers\KategoriPembayaranController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\KategoriProdukController;

Route::resource('custom-url', CabangLokasiController::class);
Route::resource('custom-url', KategoriProdukController::class);
Route::resource('custom-url', KategoriPengeluaranController::class);
Route::resource('custom-url', KategoriPembayaranController::class);
