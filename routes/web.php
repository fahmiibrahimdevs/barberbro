<?php

use App\Livewire\Example\Example;
use App\Livewire\Profile\Profile;
use App\Livewire\Dashboard\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Control\User as ControlUser;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\DataMaster\DaftarKaryawan;
use App\Livewire\DataMaster\DaftarPelanggan;
use App\Livewire\DataMaster\Produk;
use App\Livewire\DataPendukung\CabangLokasi;
use App\Livewire\DataPendukung\KategoriPembayaran;
use App\Livewire\DataPendukung\KategoriPengeluaran;
use App\Livewire\DataPendukung\KategoriProduk;
use App\Livewire\DataPendukung\KategoriSatuan;
use App\Livewire\Persediaan\KartuStok;
use App\Livewire\Persediaan\SaldoAwalItem;
use App\Livewire\Persediaan\StokKeluar;
use App\Livewire\Persediaan\StokMasuk;
use App\Livewire\Persediaan\StokOpname;

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class);
});

Route::group(['middleware' => ['auth', 'role:direktur']], function () {
    Route::get('/cabang-lokasi', CabangLokasi::class);
    Route::get('/kategori/produk', KategoriProduk::class);
    Route::get('/kategori/pengeluaran', KategoriPengeluaran::class);
    Route::get('/kategori/pembayaran', KategoriPembayaran::class);
    Route::get('/kategori/satuan', KategoriSatuan::class);
    Route::get('/master-data/produk', Produk::class);
    Route::get('/master-data/daftar-pelanggan', DaftarPelanggan::class);
    Route::get('/master-data/daftar-karyawan', DaftarKaryawan::class);
    Route::get('/persediaan/saldo-awal-item', SaldoAwalItem::class);
    Route::get('/persediaan/stok-masuk', StokMasuk::class);
    Route::get('/persediaan/stok-keluar', StokKeluar::class);
    Route::get('/persediaan/stok-opname', StokOpname::class);
    Route::get('/persediaan/kartu-stok', KartuStok::class);
    // Route::get('/example', Example::class);
    Route::get('/pengaturan/control-user', ControlUser::class);
});

Route::group(['middleware' => ['auth', 'role:user']], function () {});
require __DIR__ . '/auth.php';
