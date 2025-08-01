<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ $title }} - App Livewire</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('/assets/stisla/css/bootstrap.min.css') }}" />
    {{-- <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.2.0/css/all.css" />
    <link rel="stylesheet" href="https://static.fontawesome.com/css/fontawesome-app.css" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-pro/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/midragon/css/custom.css') }}">
    <link rel="icon" href="{{ asset('/assets/MIDRAGON.png') }}">

    @stack('general-css')
    <style>
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

    </style>

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/stisla/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/stisla/css/components.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="layout-3" style="font-family: 'Inter', sans-serif">
    <div id="app">
        {{-- tw-bg-[#778e97] --}}
        <div class="main-wrapper container">
            <div class="navbar-bg "></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <a href="{{ url('dashboard') }}" class="navbar-brand sidebar-gone-hide">BARBERBRO</a>
                <div class="navbar-nav">
                    <a href="#" class="nav-link sidebar-gone-show" data-toggle="sidebar">
                        <i class="fas fa-bars"></i>
                    </a>
                </div>
                <form class="form-inline ml-auto">
                </form>
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-title">Logged in 5 min ago</div>
                            <a href="/profile" class="dropdown-item has-icon">
                                <i class="far fa-user"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" class="dropdown-item text-danger has-icon" onclick="event.preventDefault();
                                this.closest('form').submit();">
                                    <i class="far fa-sign-out-alt"></i> Logout
                                </a>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>

            <nav class="navbar navbar-secondary navbar-expand-lg">
                <div class="container">
                    <ul class="navbar-nav">
                        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="/dashboard" class="nav-link">
                                <i class="far fa-home"></i><span>Dashboard</span>
                            </a>
                        </li>
                        @if (Auth::user()->hasRole('direktur'))
                        <li class="nav-item dropdown {{ 
                            request()->is('kategori/produk') || request()->is('kategori/keuangan') || request()->is('kategori/pembayaran') || request()->is('kategori/satuan') || request()->is('cabang-lokasi') || request()->is('master-data/produk') || request()->is('master-data/daftar-pelanggan') || request()->is('master-data/daftar-karyawan')
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown"><i
                                    class="far fa-clone"></i><span>Master Data</span></a>
                            <ul class="dropdown-menu">
                                <div class="dropdown-title">DATA PENDUKUNG</div>
                                <li class="nav-item {{ request()->is('cabang-lokasi') ? 'active' : '' }}">
                                    <a href="/cabang-lokasi" class="nav-link">Cabang Lokasi</a>
                                </li>
                                <li class="nav-item {{ request()->is('kategori/produk') ? 'active' : '' }}">
                                    <a href="/kategori/produk" class="nav-link">Kategori Produk</a>
                                </li>
                                <li class="nav-item {{ request()->is('kategori/keuangan') ? 'active' : '' }}">
                                    <a href="/kategori/keuangan" class="nav-link">Kategori Keuangan</a>
                                </li>
                                <li class="nav-item {{ request()->is('kategori/pembayaran') ? 'active' : '' }}">
                                    <a href="/kategori/pembayaran" class="nav-link">Kategori Pembayaran</a>
                                </li>
                                <li class="nav-item {{ request()->is('kategori/satuan') ? 'active' : '' }}">
                                    <a href="/kategori/satuan" class="nav-link">Kategori Satuan</a>
                                </li>
                                <div class="dropdown-title">DATA DATA</div>
                                <li class="nav-item {{ request()->is('master-data/produk') ? 'active' : '' }}">
                                    <a href="/master-data/produk" class="nav-link">Produk</a>
                                </li>
                                <li
                                    class="nav-item {{ request()->is('master-data/daftar-pelanggan') ? 'active' : '' }}">
                                    <a href="/master-data/daftar-pelanggan" class="nav-link">Daftar Pelanggan</a>
                                </li>
                                <li class="nav-item {{ request()->is('master-data/daftar-karyawan') ? 'active' : '' }}">
                                    <a href="/master-data/daftar-karyawan" class="nav-link">Daftar Karyawan</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ 
                            request()->is('persediaan/stok-masuk') || request()->is('persediaan/stok-keluar') || request()->is('persediaan/saldo-awal-item') || request()->is('persediaan/stok-opname') || request()->is('persediaan/kartu-stok')
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown">
                                <i class="far fa-inventory"></i><span>Persediaan</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item {{ request()->is('persediaan/saldo-awal-item') ? 'active' : '' }}">
                                    <a href="/persediaan/saldo-awal-item" class="nav-link">Saldo Awal Item</a>
                                </li>
                                <li class="nav-item {{ request()->is('persediaan/stok-masuk') ? 'active' : '' }}">
                                    <a href="/persediaan/stok-masuk" class="nav-link">Stok Masuk</a>
                                </li>
                                <li class="nav-item {{ request()->is('persediaan/stok-keluar') ? 'active' : '' }}">
                                    <a href="/persediaan/stok-keluar" class="nav-link">Stok Keluar</a>
                                </li>
                                <li class="nav-item {{ request()->is('persediaan/stok-opname') ? 'active' : '' }}">
                                    <a href="/persediaan/stok-opname" class="nav-link">Stok Opname</a>
                                </li>
                                <li class="nav-item {{ request()->is('persediaan/kartu-stok') ? 'active' : '' }}">
                                    <a href="/persediaan/kartu-stok" class="nav-link">Kartu Stok</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ 
                            request()->is('transaksi/jadwal-booking') || request()->is('transaksi') 
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown">
                                <i class="far fa-cash-register"></i><span>Transaksi</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item {{ request()->is('transaksi') ? 'active' : '' }}">
                                    <a href="/transaksi" class="nav-link">Transaksi</a>
                                </li>
                                <li class="nav-item {{ request()->is('transaksi/jadwal-booking') ? 'active' : '' }}">
                                    <a href="/transaksi/jadwal-booking" class="nav-link">Jadwal Booking</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ 
                            request()->is('keuangan/cash-on-bank') || request()->is('keuangan/pengeluaran') || request()->is('keuangan/buku-besar') || request()->is('keuangan/slip-gaji') || request()->is('keuangan/kasbon')
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown">
                                <i class="far fa-money-bill"></i><span>Keuangan</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item {{ request()->is('keuangan/cash-on-bank') ? 'active' : '' }}">
                                    <a href="/keuangan/cash-on-bank" class="nav-link">Cash on Bank</a>
                                </li>
                                <li class="nav-item {{ request()->is('keuangan/kasbon') ? 'active' : '' }}">
                                    <a href="/keuangan/kasbon" class="nav-link">Kasbon</a>
                                </li>
                                <li class="nav-item {{ request()->is('keuangan/slip-gaji') ? 'active' : '' }}">
                                    <a href="/keuangan/slip-gaji" class="nav-link">Slip Gaji</a>
                                </li>
                                <li class="nav-item {{ request()->is('keuangan/buku-besar') ? 'active' : '' }}">
                                    <a href="/keuangan/buku-besar" class="nav-link">Buku Besar</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ 
                            request()->is('laporan/transaksi') || request()->is('laporan/pembayaran-non-tunai') || request()->is('laporan/komisi-karyawan') || request()->is('laporan/pengeluaran')
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown">
                                <i class="far fa-files"></i><span>Laporan</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item {{ request()->is('laporan/transaksi') ? 'active' : '' }}">
                                    <a href="/laporan/transaksi" class="nav-link">Laporan Transaksi</a>
                                </li>
                                <li
                                    class="nav-item {{ request()->is('laporan/pembayaran-non-tunai') ? 'active' : '' }}">
                                    <a href="/laporan/pembayaran-non-tunai" class="nav-link">Laporan Pembayaran Non
                                        Tunai</a>
                                </li>
                                <li class="nav-item {{ request()->is('laporan/komisi-karyawan') ? 'active' : '' }}">
                                    <a href="/laporan/komisi-karyawan" class="nav-link">Laporan Komisi Karyawan</a>
                                </li>
                                <li class="nav-item {{ request()->is('laporan/pengeluaran') ? 'active' : '' }}">
                                    <a href="/laporan/pengeluaran" class="nav-link">Laporan Pengeluaran</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ 
                            request()->is('pengaturan/profile-usaha') || request()->is('pengaturan/reset-no-transaksi') || request()->is('pengaturan/backup-restore') || request()->is('pengaturan/control-user')
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown">
                                <i class="far fa-cogs"></i><span>Pengaturan</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item {{ request()->is('pengaturan/profile-usaha') ? 'active' : '' }}">
                                    <a href="/pengaturan/profile-usaha" class="nav-link">Profile Usaha</a>
                                </li>
                                <li
                                    class="nav-item {{ request()->is('pengaturan/reset-no-transaksi') ? 'active' : '' }}">
                                    <a href="/pengaturan/reset-no-transaksi" class="nav-link">Reset No. Transaksi</a>
                                </li>
                                <li class="nav-item {{ request()->is('pengaturan/backup-restore') ? 'active' : '' }}">
                                    <a href="/pengaturan/backup-restore" class="nav-link">Backup & Restore</a>
                                </li>
                                <li class="nav-item {{ request()->is('pengaturan/control-user') ? 'active' : '' }}">
                                    <a href="/pengaturan/control-user" class="nav-link">Control User</a>
                                </li>
                            </ul>
                        </li>
                        @elseif (Auth::user()->hasRole('admin'))
                        <li class="nav-item dropdown {{ 
                            request()->is('admin/kategori/produk') || request()->is('admin/kategori/keuangan') || request()->is('admin/kategori/pembayaran') || request()->is('admin/kategori/satuan') || request()->is('admin/master-data/produk') || request()->is('admin/master-data/daftar-pelanggan') || request()->is('admin/master-data/daftar-karyawan')
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown"><i
                                    class="far fa-clone"></i><span>Master Data</span></a>
                            <ul class="dropdown-menu">
                                <div class="dropdown-title">DATA PENDUKUNG</div>
                                <li class="nav-item {{ request()->is('admin/kategori/produk') ? 'active' : '' }}">
                                    <a href="/admin/kategori/produk" class="nav-link">Kategori Produk</a>
                                </li>
                                <li class="nav-item {{ request()->is('admin/kategori/keuangan') ? 'active' : '' }}">
                                    <a href="/admin/kategori/keuangan" class="nav-link">Kategori Keuangan</a>
                                </li>
                                <li class="nav-item {{ request()->is('admin/kategori/pembayaran') ? 'active' : '' }}">
                                    <a href="/admin/kategori/pembayaran" class="nav-link">Kategori Pembayaran</a>
                                </li>
                                <li class="nav-item {{ request()->is('admin/kategori/satuan') ? 'active' : '' }}">
                                    <a href="/admin/kategori/satuan" class="nav-link">Kategori Satuan</a>
                                </li>
                                <div class="dropdown-title">DATA DATA</div>
                                <li class="nav-item {{ request()->is('admin/master-data/produk') ? 'active' : '' }}">
                                    <a href="/admin/master-data/produk" class="nav-link">Produk</a>
                                </li>
                                <li
                                    class="nav-item {{ request()->is('admin/master-data/daftar-pelanggan') ? 'active' : '' }}">
                                    <a href="/admin/master-data/daftar-pelanggan" class="nav-link">Daftar Pelanggan</a>
                                </li>
                                <li
                                    class="nav-item {{ request()->is('admin/master-data/daftar-karyawan') ? 'active' : '' }}">
                                    <a href="/admin/master-data/daftar-karyawan" class="nav-link">Daftar Karyawan</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ 
                            request()->is('admin/persediaan/stok-masuk') || request()->is('admin/persediaan/stok-keluar') || request()->is('admin/persediaan/saldo-awal-item') || request()->is('admin/persediaan/stok-opname') || request()->is('admin/persediaan/kartu-stok')
                            ? 'active' : '' }}">
                            <a href="#" data-toggle="dropdown" class="nav-link has-dropdown">
                                <i class="far fa-inventory"></i><span>Persediaan</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li
                                    class="nav-item {{ request()->is('admin/persediaan/saldo-awal-item') ? 'active' : '' }}">
                                    <a href="/admin/persediaan/saldo-awal-item" class="nav-link">Saldo Awal Item</a>
                                </li>
                                <li class="nav-item {{ request()->is('admin/persediaan/stok-masuk') ? 'active' : '' }}">
                                    <a href="/admin/persediaan/stok-masuk" class="nav-link">Stok Masuk</a>
                                </li>
                                <li
                                    class="nav-item {{ request()->is('admin/persediaan/stok-keluar') ? 'active' : '' }}">
                                    <a href="/admin/persediaan/stok-keluar" class="nav-link">Stok Keluar</a>
                                </li>
                                <li
                                    class="nav-item {{ request()->is('admin/persediaan/stok-opname') ? 'active' : '' }}">
                                    <a href="/admin/persediaan/stok-opname" class="nav-link">Stok Opname</a>
                                </li>
                                <li class="nav-item {{ request()->is('admin/persediaan/kartu-stok') ? 'active' : '' }}">
                                    <a href="/admin/persediaan/kartu-stok" class="nav-link">Kartu Stok</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="main-content">
                {{ $slot }}
            </div>
            <footer class="main-footer">
                <div class="footer-left">
                    Copyright &copy; 2025 <div class="bullet"></div> Created By <a
                        href="http://fahmiibrahimdev.tech/">Fahmi Ibrahim</a>
                </div>
                <div class="footer-right">
                    1.2.0
                </div>
            </footer>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('/assets/midragon/select2/jquery.min.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>

    <!-- JS Libraies -->
    <script src="{{ asset('assets/midragon/js/sweetalert2@11.js') }}"></script>
    @stack('js-libraries')

    <!-- Page Specific JS File -->
    <script src="{{ asset('/assets/stisla/js/stisla.js') }}"></script>
    <script>
        window.addEventListener('swal:modal', event => {
            Swal.fire({
                title: event.detail[0].message,
                text: event.detail[0].text,
                icon: event.detail[0].type,
            })
            $("#formDataModal").modal("hide");
        })
        window.addEventListener('swal:confirm', event => {
            Swal.fire({
                title: event.detail[0].message,
                text: event.detail[0].text,
                icon: event.detail[0].type,
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete')
                }
            })
        })
        window.addEventListener('swal:transaksi', function (event) {
            const {
                idTransaksi,
                message,
                text
            } = event.detail[0];

            Swal.fire({
                title: message,
                text: text,
                icon: 'success',
                showCancelButton: true,
                cancelButtonText: 'Tidak',
                confirmButtonText: 'Ya, Cetak Struk',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('/transaksi/print-nota/' + idTransaksi, '_blank');
                }
            });
        });
        window.onbeforeunload = function () {
            window.scrollTo(5, 75);
        };

    </script>

    <!-- Template JS File -->
    <script src="{{ asset('/assets/stisla/js/scripts.js') }}"></script>
    <script src="{{ asset('/assets/stisla/js/custom.js') }}"></script>
    @stack('scripts')
</body>

</html>
