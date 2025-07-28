<?php

use App\Livewire\Example\Example;
use App\Livewire\Profile\Profile;
use App\Livewire\Dashboard\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Control\User as ControlUser;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\Admin\DataMaster\DaftarKaryawan as AdminDaftarKaryawan;
use App\Livewire\Admin\DataMaster\DaftarPelanggan as AdminDaftarPelanggan;
use App\Livewire\Admin\DataMaster\Produk as AdminProduk;
use App\Livewire\Admin\Persediaan\KartuStok as AdminKartuStok;
use App\Livewire\Admin\Persediaan\SaldoAwalItem as AdminSaldoAwalItem;
use App\Livewire\Admin\Persediaan\StokKeluar as AdminStokKeluar;
use App\Livewire\Admin\Persediaan\StokMasuk as AdminStokMasuk;
use App\Livewire\Admin\Persediaan\StokOpname as AdminStokOpname;
use App\Livewire\DataMaster\DaftarKaryawan;
use App\Livewire\DataMaster\DaftarPelanggan;
use App\Livewire\DataMaster\Produk;
use App\Livewire\DataPendukung\CabangLokasi;
use App\Livewire\DataPendukung\KategoriKeuangan;
use App\Livewire\DataPendukung\KategoriPembayaran;
use App\Livewire\DataPendukung\KategoriProduk;
use App\Livewire\DataPendukung\KategoriSatuan;
use App\Livewire\Keuangan\CashOnBank;
use App\Livewire\Persediaan\KartuStok;
use App\Livewire\Persediaan\SaldoAwalItem;
use App\Livewire\Persediaan\StokKeluar;
use App\Livewire\Persediaan\StokMasuk;
use App\Livewire\Persediaan\StokOpname;
use App\Livewire\Test;
use App\Livewire\Transaksi\Transaksi;

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store']);

Route::get('test', Test::class);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class);
});

Route::group(['middleware' => ['auth', 'role:direktur']], function () {
    Route::get('/cabang-lokasi', CabangLokasi::class);
    Route::get('/kategori/produk', KategoriProduk::class);
    Route::get('/kategori/keuangan', KategoriKeuangan::class);
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

    Route::get('/keuangan/cash-on-bank', CashOnBank::class);

    Route::get('/transaksi', Transaksi::class);
    // Route::get('/example', Example::class);
    Route::get('/pengaturan/control-user', ControlUser::class);
});

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/admin/kategori/produk', KategoriProduk::class);
    Route::get('/admin/kategori/keuangan', KategoriKeuangan::class);
    Route::get('/admin/kategori/pembayaran', KategoriPembayaran::class);
    Route::get('/admin/kategori/satuan', KategoriSatuan::class);
    Route::get('/admin/master-data/produk', AdminProduk::class);
    Route::get('/admin/master-data/daftar-pelanggan', AdminDaftarPelanggan::class);
    Route::get('/admin/master-data/daftar-karyawan', AdminDaftarKaryawan::class);
    Route::get('/admin/persediaan/saldo-awal-item', AdminSaldoAwalItem::class);
    Route::get('/admin/persediaan/stok-masuk', AdminStokMasuk::class);
    Route::get('/admin/persediaan/stok-keluar', AdminStokKeluar::class);
    Route::get('/admin/persediaan/stok-opname', AdminStokOpname::class);
    Route::get('/admin/persediaan/kartu-stok', AdminKartuStok::class);

    // Route::get('/keuangan/cash-on-bank', CashOnBank::class);

    // Route::get('/transaksi', Transaksi::class);
    // // Route::get('/example', Example::class);
    // Route::get('/pengaturan/control-user', ControlUser::class);
});

Route::group(['middleware' => ['auth', 'role:user']], function () {});
require __DIR__ . '/auth.php';
