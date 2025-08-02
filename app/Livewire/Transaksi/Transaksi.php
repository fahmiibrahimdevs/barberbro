<?php

namespace App\Livewire\Transaksi;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\DetailTransaksi;
use App\Models\TransaksiCounter;
use Illuminate\Support\Facades\DB;
use App\Services\GlobalDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Transaksi as ModelsTransaksi;

class Transaksi extends Component
{
    use WithPagination;
    #[Title('Transaksi')]

    protected $paginationTheme = 'bootstrap';
    protected $globalDataService;

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        // 'no_transaksi' => 'required',
        'id_cabang' => 'required',
    ];

    public $lengthData = 25;
    public $searchTerm, $searchProduk;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    public $cabangs, $pelanggans, $produks, $pembayarans;
    /**
     * @var \Illuminate\Support\Collection
     */
    public $karyawans = [];
    public $cartItems = [];
    public $isPersentase = false;
    public $check_id_kategori;

    public $filter_status, $filter_pembayaran = '';

    // Table: transaksi
    public $id_cabang, $id_user, $no_transaksi, $tanggal, $id_pelanggan, $catatan, $total_pesanan, $total_komisi, $total_sub_total, $total_diskon, $total_akhir, $laba_bersih, $id_metode_pembayaran, $jumlah_dibayarkan, $kembalian, $status;

    // Table: detail_transaksi
    // public $id_transaksi, $input_id_produk, $display_nama_item, $display_kategori_item, $display_deskripsi_item, $input_harga_item, $jumlah, $input_sub_total, $input_diskon, $display_diskon, $input_total_harga, $id_karyawan, $display_nama_karyawan, $input_komisi, $display_komisi;

    // Table: detail_transaksi
    public $id_transaksi, $id_produk, $nama_item, $kategori_item, $deskripsi_item, $harga_item, $jumlah, $sub_total, $input_diskon, $diskon, $total_harga, $id_karyawan, $nama_karyawan, $komisi_persen, $komisi_nominal;

    public function mount(GlobalDataService $globalDataService)
    {
        $this->globalDataService = $globalDataService;
        $this->cabangs           = $this->globalDataService->getCabangs();
        $this->id_cabang         = $this->cabangs->first()->id;

        $this->pelanggans        = $this->globalDataService->getPelanggansCustom($this->id_cabang);
        $this->produks           = $this->globalDataService->getProdukAndKategoriCustom($this->id_cabang);
        $this->pembayarans       = $this->globalDataService->getMetodePembayaran();

        // $this->id_cabang            = "1";
        $this->id_user              = Auth::user()->id;

        $this->resetInputFields();
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = DB::table('transaksi')->select('transaksi.id', 'transaksi.no_transaksi', 'transaksi.tanggal', 'daftar_pelanggan.nama_pelanggan', 'daftar_pelanggan.no_telp',  'transaksi.total_akhir', 'transaksi.status', 'detail.nama_item', 'detail.deskripsi_item', 'jumlah.jumlah_produk', 'kategori_pembayaran.nama_kategori', 'cabang_lokasi.nama_cabang')
            ->join('daftar_pelanggan', 'daftar_pelanggan.id', 'transaksi.id_pelanggan')
            ->join('kategori_pembayaran', 'kategori_pembayaran.id', 'transaksi.id_metode_pembayaran')
            ->join('cabang_lokasi', 'cabang_lokasi.id', 'transaksi.id_cabang')
            ->leftJoin(DB::raw('(
                SELECT id_transaksi, nama_item, deskripsi_item
                FROM detail_transaksi
                GROUP BY id_transaksi
            ) AS detail'), 'detail.id_transaksi', '=', 'transaksi.id')
            ->leftJoin(DB::raw('(
                SELECT id_transaksi, COUNT(*) as jumlah_produk
                FROM detail_transaksi
                GROUP BY id_transaksi
            ) AS jumlah'), 'jumlah.id_transaksi', '=', 'transaksi.id')
            ->where(function ($query) use ($search) {
                $query->where('no_transaksi', 'LIKE', $search);
                $query->orWhere('nama_pelanggan', 'LIKE', $search);
            })
            ->when($this->filter_status, function ($query) {
                $query->where('transaksi.status', $this->filter_status);
            })
            ->when($this->filter_pembayaran, function ($query) {
                $query->where('transaksi.id_metode_pembayaran', $this->filter_pembayaran);
            })
            ->whereBetween('transaksi.tanggal', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->orderBy('transaksi.id', 'DESC')
            ->orderBy('transaksi.no_transaksi', 'DESC')
            ->orderBy('transaksi.tanggal', 'DESC')
            ->paginate($this->lengthData);

        return view('livewire.transaksi.transaksi', compact('data'));
    }

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $this->kembalian = $this->jumlah_dibayarkan - $this->total_akhir;
            $this->kembalian < 0 ? $status = "belum lunas" : $status = "lunas";

            $no_transaksi = $this->generateNoTransaksi($this->id_cabang);
            $transaksi = ModelsTransaksi::create([
                'id_cabang'             => $this->id_cabang,
                'id_user'               => $this->id_user,
                'no_transaksi'          => $no_transaksi,
                'tanggal'               => date('Y-m-d H:i:s'),
                'id_pelanggan'          => $this->id_pelanggan,
                'catatan'               => $this->catatan,
                'total_pesanan'         => $this->total_pesanan,
                'total_komisi_karyawan' => $this->total_komisi,
                'total_sub_total'       => $this->total_sub_total,
                'total_diskon'          => $this->total_diskon,
                'total_akhir'           => $this->total_akhir,
                'laba_bersih'           => $this->total_akhir - $this->total_komisi,
                'id_metode_pembayaran'  => $this->id_metode_pembayaran,
                'jumlah_dibayarkan'     => $this->jumlah_dibayarkan,
                'kembalian'             => $this->kembalian,
                'status'                => $status,
            ]);

            $detailData = [];

            foreach ($this->cartItems as $item) {
                $detailData[] = [
                    'id_transaksi'   => $transaksi->id,
                    'id_produk'      => $item['id_produk'],
                    'nama_item'      => $item['nama_item'],
                    'kategori_item'  => $item['kategori_item'],
                    'deskripsi_item' => $item['deskripsi_item'],
                    'harga'          => $item['harga'],
                    'jumlah'         => $item['jumlah'],
                    'sub_total'      => $item['sub_total'],
                    'diskon'         => $item['diskon'],
                    'total_harga'    => $item['total_harga'],
                    'id_karyawan'    => $item['id_karyawan'],
                    'nama_karyawan'  => $item['nama_karyawan'],
                    'komisi_persen'  => $item['komisi_persen'],
                    'komisi_nominal' => $item['komisi_nominal'],
                ];
            }

            DetailTransaksi::insert($detailData);
            DB::commit();
            $this->dispatchSwalTransaksi($transaksi->id);
            $this->resetInputFields();
            $this->dispatch('closePembayaranModal');
            $this->dispatch('printNota', id: $transaksi->id);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchAlert('error', 'Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function isEditingMode($mode)
    {
        $this->isEditing = $mode;
        $this->initSelect2();
    }

    public function edit(GlobalDataService $globalDataService, $id)
    {
        $this->isEditing = true;
        $this->dataId = $id;

        // Ambil data transaksi utama
        $transaksi = ModelsTransaksi::findOrFail($id);

        $this->id_cabang            = $transaksi->id_cabang;
        $this->id_user              = $transaksi->id_user;
        $this->no_transaksi         = $transaksi->no_transaksi;
        $this->tanggal              = $transaksi->tanggal;
        $this->id_pelanggan         = $transaksi->id_pelanggan;
        $this->catatan              = $transaksi->catatan;
        $this->total_pesanan        = $transaksi->total_pesanan;
        $this->total_komisi         = $transaksi->total_komisi_karyawan;
        $this->total_sub_total      = $transaksi->total_sub_total;
        $this->total_diskon         = $transaksi->total_diskon;
        $this->total_akhir          = $transaksi->total_akhir;
        $this->laba_bersih          = $transaksi->laba_bersih;
        $this->id_metode_pembayaran = $transaksi->id_metode_pembayaran;
        $this->jumlah_dibayarkan    = $transaksi->jumlah_dibayarkan;
        $this->kembalian            = $transaksi->kembalian;
        $this->status               = $transaksi->status;

        $this->pelanggans = $globalDataService->getPelanggansCustom($this->id_cabang);
        $this->produks    = $globalDataService->getProdukAndKategoriCustom($this->id_cabang);

        // Ambil detail transaksi
        $detail = DetailTransaksi::where('id_transaksi', $id)->get();

        $this->cartItems = [];

        foreach ($detail as $item) {
            $this->cartItems[] = [
                'id_produk'      => $item->id_produk,
                'nama_item'      => $item->nama_item,
                'kategori_item'  => $item->kategori_item,
                'deskripsi_item' => $item->deskripsi_item,
                'harga'          => $item->harga,  // Hati-hati dengan nama kolom
                'jumlah'         => $item->jumlah,
                'sub_total'      => $item->sub_total,
                'diskon'         => $item->diskon,
                'total_harga'    => $item->total_harga,
                'id_karyawan'    => $item->id_karyawan,
                'nama_karyawan'  => $item->nama_karyawan,
                'komisi_persen'  => $item->komisi_persen,
                'komisi_nominal' => $item->komisi_nominal,
            ];
        }

        $this->initSelect2(); // Jika ini penting untuk inisialisasi UI
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            if ($this->dataId) {
                $this->kembalian = $this->jumlah_dibayarkan - $this->total_akhir;
                $this->kembalian < 0 ? $status = "belum lunas" : $status = "lunas";

                // $no_transaksi = $this->generateNoTransaksi($this->id_cabang);

                // Ambil instance model transaksi
                $transaksi = ModelsTransaksi::findOrFail($this->dataId);

                // Update data transaksi
                $transaksi->update([
                    'id_user'               => $this->id_user,
                    // 'no_transaksi'          => $no_transaksi,
                    'id_pelanggan'          => $this->id_pelanggan,
                    'catatan'               => $this->catatan,
                    'total_pesanan'         => $this->total_pesanan,
                    'total_komisi_karyawan' => $this->total_komisi,
                    'total_sub_total'       => $this->total_sub_total,
                    'total_diskon'          => $this->total_diskon,
                    'total_akhir'           => $this->total_akhir,
                    'laba_bersih'           => $this->total_akhir - $this->total_komisi,
                    'id_metode_pembayaran'  => $this->id_metode_pembayaran,
                    'jumlah_dibayarkan'     => $this->jumlah_dibayarkan,
                    'kembalian'             => $this->kembalian,
                    'status'                => $status,
                ]);

                // Hapus data detail transaksi lama
                DetailTransaksi::where('id_transaksi', $transaksi->id)->delete();

                // Insert data detail transaksi baru
                $detailData = [];
                foreach ($this->cartItems as $item) {
                    $detailData[] = [
                        'id_transaksi'   => $transaksi->id,
                        'id_produk'      => $item['id_produk'],
                        'nama_item'      => $item['nama_item'],
                        'kategori_item'  => $item['kategori_item'],
                        'deskripsi_item' => $item['deskripsi_item'],
                        'harga'          => $item['harga'],
                        'jumlah'         => $item['jumlah'],
                        'sub_total'      => $item['sub_total'],
                        'diskon'         => $item['diskon'],
                        'total_harga'    => $item['total_harga'],
                        'id_karyawan'    => $item['id_karyawan'],
                        'nama_karyawan'  => $item['nama_karyawan'],
                        'komisi_persen'  => $item['komisi_persen'],
                        'komisi_nominal' => $item['komisi_nominal'],
                    ];
                }

                DetailTransaksi::insert($detailData);
            }

            DB::commit();
            $this->dispatchSwalTransaksi($transaksi->id);
            $this->dataId = null;
            $this->dispatch('closePembayaranModal');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchAlert('error', 'Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteConfirm($id)
    {
        $this->dataId = $id;
        $this->dispatch('swal:confirm', [
            'type'      => 'warning',
            'message'   => 'Are you sure?',
            'text'      => 'If you delete the data, it cannot be restored!'
        ]);
    }

    public function delete()
    {
        ModelsTransaksi::findOrFail($this->dataId)->delete();
        $this->dispatchAlert('success', 'Success!', 'Data deleted successfully.');
    }

    public function updatingLengthData()
    {
        $this->resetPage();
    }

    private function searchResetPage()
    {
        if ($this->searchTerm !== $this->previousSearchTerm) {
            $this->resetPage();
        }

        $this->previousSearchTerm = $this->searchTerm;
    }

    private function dispatchAlert($type, $message, $text)
    {
        $this->dispatch('swal:modal', [
            'type'      => $type,
            'message'   => $message,
            'text'      => $text
        ]);

        $this->resetInputFields();
    }

    private function dispatchSwalTransaksi($idTransaksi)
    {
        $this->dispatch('swal:transaksi', [
            'idTransaksi' => Crypt::encrypt($idTransaksi), // enkripsi ID,
            'message'     => 'Transaksi berhasil!',
            'text'        => 'Apakah kamu ingin mencetak struk sekarang?',
        ]);
    }

    public function updatedIdCabang(GlobalDataService $globalDataService)
    {
        $this->pelanggans = $globalDataService->getPelanggansCustom($this->id_cabang);
        $this->produks    = $globalDataService->getProdukAndKategoriCustom($this->id_cabang);
        $this->karyawans  = $globalDataService->getKaryawansCustom($this->id_cabang);

        $this->dispatch('refreshList');

        // Reset cart items when changing cabang
        $this->cartItems = [];
        $this->resetInputFields();
    }

    /* 
    | ======================================================
    |  <formListItemsModal> - Produk & Pilih Karyawan
    | ======================================================
    |
    |  listProduk(): Inisialisasi modal produk
    |     - Memanggil initSelect2 agar select box ter-render ulang
    |     - Digunakan saat modal pertama kali dibuka
    |
    |  updatedSearchProduk(): Pencarian produk live
    |     - Melakukan pencarian nama_item atau deskripsi produk
    |       berdasarkan input pengguna (LIKE %keyword%)
    |     - Data yang diambil termasuk nama, harga, kategori, dsb.
    |
    |  cartProduk($id): Ambil dan proses data produk yang dipilih
    |     - Fetch data produk dan isi ke form input (nama, harga, dsb.)
    |     - Hitung sub_total dan total_harga awal berdasarkan jumlah
    |     - Jika kategori tertentu (misalnya jasa), filter capster
    |       dari tabel karyawan yang punya komisi
    |     - Jika kategori lain, ambil semua capster dan kasir
    |     - Kosongkan id_karyawan terlebih dahulu, lalu trigger logika
    |     - Akhiri dengan re-inisialisasi Select2
    |
    */
    public function listProduk()
    {
        $this->initSelect2();
    }

    public function updatedSearchProduk()
    {
        $search = '%' . $this->searchProduk . '%';

        $this->produks = DB::table('produk')->select('produk.id', 'nama_item', 'harga_jasa', 'nama_kategori', 'produk.deskripsi')
            ->join('kategori_produk', 'kategori_produk.id', 'produk.id_kategori')
            ->where('produk.id_cabang', $this->id_cabang)
            ->where(function ($query) use ($search) {
                $query->where('nama_item', 'LIKE', $search);
                $query->orWhere('produk.deskripsi', 'LIKE', $search);
            })
            ->get();
    }

    public function cartProduk($id)
    {
        $data = DB::table('produk')->select('produk.id', 'produk.id_kategori', 'nama_item', 'harga_jasa', 'nama_kategori', 'produk.deskripsi')
            ->join('kategori_produk', 'kategori_produk.id', 'produk.id_kategori')
            ->where('produk.id', $id)
            ->first();
        $this->id_produk         = $data->id;
        $this->check_id_kategori = $data->id_kategori;
        $this->nama_item         = $data->nama_item;
        $this->deskripsi_item    = $data->deskripsi;
        $this->kategori_item     = $data->nama_kategori;
        $this->harga_item        = $data->harga_jasa;
        $this->sub_total         = $this->harga_item * $this->jumlah;
        $this->total_harga       = $this->sub_total;

        if (in_array($this->check_id_kategori, ['2', '3'])) {
            $this->karyawans = DB::table('daftar_karyawan')
                ->select('daftar_karyawan.id', 'users.name')
                ->join('users', 'users.id', '=', 'daftar_karyawan.id_user')
                ->join('komisi', 'komisi.id_karyawan', '=', 'daftar_karyawan.id')
                ->where('daftar_karyawan.role_id', 'capster')
                ->where('komisi.id_produk', $data->id)
                ->where('komisi.komisi_persen', '>', 0)
                ->where('daftar_karyawan.id_cabang', $this->id_cabang)
                ->distinct()
                ->get();
        } else if ($this->check_id_kategori == "4") {
            $this->karyawans = DB::table('daftar_karyawan')
                ->select('daftar_karyawan.id', 'users.name')
                ->join('users', 'users.id', '=', 'daftar_karyawan.id_user')
                ->whereIn('daftar_karyawan.role_id', ['kasir'])
                ->where('daftar_karyawan.id_cabang', $this->id_cabang)
                ->get();
        } else {
            $this->karyawans = DB::table('daftar_karyawan')
                ->select('daftar_karyawan.id', 'users.name')
                ->join('users', 'users.id', '=', 'daftar_karyawan.id_user')
                ->whereIn('daftar_karyawan.role_id', ['capster', 'kasir'])
                ->where('daftar_karyawan.id_cabang', $this->id_cabang)
                ->get();
        }

        // $this->id_karyawan = $this->karyawans->first()->id ?? null;
        $this->id_karyawan = '';
        $this->updatedIdKaryawan();

        // dd($this->id_karyawan);

        $this->initSelect2();
    }
    // ---------------------- END formListItemsModal ------------------------

    /* 
    | ===========================================================
    | <formCartItemsModal> - Logika Penambahan Item ke Keranjang
    | ===========================================================
    |
    | updatedIdKaryawan()
    |     - Mengambil persentase komisi berdasarkan karyawan & produk
    |     - Hitung komisi nominal dari subtotal
    |
    | incrementJumlah(), decrementJumlah()
    |     - Menambah/mengurangi jumlah item
    |     - Update subtotal, diskon (jika ada), total harga, dan komisi
    |
    | updatedIsPersentase()
    |     - Reset nilai diskon jika tipe diskon diubah (persen/nominal)
    |
    | updatedInputDiskon()
    |     - Hitung diskon berdasarkan input (persen atau nominal)
    |     - Update total harga setelah diskon
    |
    | cancelCartItems()
    |     - Reset semua nilai form input item cart
    |
    | addCartItems()
    |     - Validasi input
    |     - Ambil nama karyawan
    |     - Tambahkan item ke cartItems[]
    |     - Reset form, tutup modal, dan hitung ulang ringkasan transaksi
    |
    */
    public function updatedIdKaryawan()
    {
        // dd($this->check_id_kategori);
        if ($this->check_id_kategori == "2" || $this->check_id_kategori == "3") {
            // dd($this->id_karyawan, $this->id_produk);
            $this->komisi_persen = DB::table('komisi')
                ->select('komisi_persen')
                ->where('id_karyawan', $this->id_karyawan)
                ->where('id_produk', $this->id_produk)
                ->first()->komisi_persen ?? 0;

            $this->komisi_nominal = $this->sub_total * $this->komisi_persen / 100;
            // dd($this->komisi_persen);
        } else {
            $this->komisi_persen = DB::table('produk')
                ->select('komisi')
                ->where('id', $this->id_produk)
                ->first()->komisi;

            $this->komisi_nominal = $this->sub_total * $this->komisi_persen / 100;
        }

        // dd($this->komisi_persen);
    }

    /**
     * Menambah jumlah item dalam transaksi.
     *
     * Fungsi ini digunakan untuk menambah jumlah item yang dipilih dalam transaksi.
     * Saat jumlah bertambah, subtotal akan dihitung ulang berdasarkan harga item
     * dan jumlah yang baru. Jika ada diskon yang diinputkan, maka diskon dan total
     * harga akan diperbarui. Selain itu, komisi nominal juga dihitung ulang
     * berdasarkan subtotal dan persentase komisi.
     *
     * Langkah-langkah:
     * 1. Inisialisasi ulang elemen Select2 (jika ada).
     * 2. Tambahkan nilai properti `jumlah` sebesar 1.
     * 3. Hitung ulang subtotal (`sub_total`) berdasarkan harga item dan jumlah.
     * 4. Jika ada diskon yang diinputkan (`input_diskon > 0`), panggil fungsi
     *    `updatedInputDiskon` untuk menghitung ulang diskon dan total harga.
     * 5. Jika tidak ada diskon, set total harga sama dengan subtotal dan reset diskon ke 0.
     * 6. Hitung ulang komisi nominal (`komisi_nominal`) berdasarkan subtotal
     *    dan persentase komisi (`komisi_persen`).
     *
     * Properti yang diperbarui:
     * - $jumlah: Jumlah item.
     * - $sub_total: Subtotal harga (harga item x jumlah).
     * - $total_harga: Total harga setelah diskon.
     * - $diskon: Nilai diskon (jika ada).
     * - $komisi_nominal: Komisi nominal berdasarkan subtotal dan persentase komisi.
     *
     * @return void
     */
    public function incrementJumlah()
    {
        $this->initSelect2();
        $this->jumlah++;
        $this->sub_total = (int)$this->harga_item * (int)$this->jumlah;

        if ($this->input_diskon > 0) {
            $this->updatedInputDiskon();
        } else {
            $this->total_harga = $this->sub_total;
            $this->diskon = 0;
        }

        $this->komisi_nominal = $this->sub_total * $this->komisi_persen / 100;
    }

    /**
     * Mengurangi jumlah item dalam transaksi.
     *
     * Fungsi ini digunakan untuk mengurangi jumlah item yang dipilih dalam transaksi.
     * Saat jumlah berkurang, subtotal akan dihitung ulang berdasarkan harga item
     * dan jumlah yang baru. Jika ada diskon yang diinputkan, maka diskon dan total
     * harga akan diperbarui. Selain itu, komisi nominal juga dihitung ulang
     * berdasarkan subtotal dan persentase komisi.
     *
     * Langkah-langkah:
     * 1. Inisialisasi ulang elemen Select2 (jika ada).
     * 2. Kurangi nilai properti `jumlah` sebesar 1, dengan batas minimum 1.
     * 3. Hitung ulang subtotal (`sub_total`) berdasarkan harga item dan jumlah.
     * 4. Jika ada diskon yang diinputkan (`input_diskon > 0`), panggil fungsi
     *    `updatedInputDiskon` untuk menghitung ulang diskon dan total harga.
     * 5. Jika tidak ada diskon, set total harga sama dengan subtotal dan reset diskon ke 0.
     * 6. Hitung ulang komisi nominal (`komisi_nominal`) berdasarkan subtotal
     *    dan persentase komisi (`komisi_persen`).
     *
     * Properti yang diperbarui:
     * - $jumlah: Jumlah item.
     * - $sub_total: Subtotal harga (harga item x jumlah).
     * - $total_harga: Total harga setelah diskon.
     * - $diskon: Nilai diskon (jika ada).
     * - $komisi_nominal: Komisi nominal berdasarkan subtotal dan persentase komisi.
     *
     * @return void
     */
    public function decrementJumlah()
    {
        $this->initSelect2();
        if ($this->jumlah > 1) {
            $this->jumlah--;
        }

        $this->sub_total = (int)$this->harga_item * (int)$this->jumlah;

        if ($this->input_diskon > 0) {
            $this->updatedInputDiskon();
        } else {
            $this->total_harga = $this->sub_total;
            $this->diskon = 0;
        }

        $this->komisi_nominal = $this->sub_total * $this->komisi_persen / 100;
    }

    public function updatedIsPersentase()
    {
        $this->input_diskon = 0;
        $this->diskon       = 0;
        $this->total_harga  = $this->sub_total;
    }

    public function updatedInputDiskon()
    {
        if ($this->isPersentase) {
            // Diskon dalam persen dari subtotal
            $this->diskon = (int)$this->sub_total * (int)$this->input_diskon / 100;
        } else {
            // Diskon dalam nominal langsung
            $this->diskon = (int)$this->input_diskon;
        }

        // Opsional: total akhir setelah diskon
        $this->total_harga = $this->sub_total - $this->diskon;
    }

    public function cancelCartItems()
    {
        $this->komisi_persen  = 0;
        $this->komisi_nominal = 0;
        $this->jumlah         = 1;
        $this->input_diskon   = 0;
        $this->diskon         = 0;
        $this->dispatch('initSelect2');
    }

    /**
     * Menambahkan item ke keranjang transaksi.
     *
     * Fungsi ini digunakan untuk menambahkan item yang dipilih ke dalam keranjang transaksi.
     * Data yang ditambahkan mencakup informasi produk, jumlah, harga, diskon, total harga,
     * serta informasi karyawan yang terkait dengan produk tersebut.
     *
     * Langkah-langkah:
     * 1. Validasi input untuk memastikan karyawan telah dipilih.
     * 2. Ambil nama karyawan berdasarkan ID karyawan yang dipilih.
     * 3. Tambahkan data item ke dalam array `$cartItems`.
     * 4. Tutup modal keranjang dengan dispatch event `closeCart`.
     * 5. Reset semua input form keranjang dengan memanggil `cancelCartItems`.
     * 6. Perbarui ringkasan transaksi dengan memanggil `getRingkasanTransaksi`.
     *
     * Properti yang diperbarui:
     * - $cartItems: Menyimpan daftar item yang ada di keranjang.
     * - $nama_karyawan: Nama karyawan yang terkait dengan item.
     *
     * @return void
     */
    public function addCartItems()
    {
        $this->dispatch('initSelect2');
        $this->validate([
            'id_karyawan' => 'required',
        ], [
            'id_karyawan.required' => 'Karyawan wajib dipilih.',
        ]);

        $this->nama_karyawan = null;

        if ($this->id_karyawan) {
            $this->nama_karyawan = DB::table('daftar_karyawan')
                ->join('users', 'users.id', '=', 'daftar_karyawan.id_user')
                ->where('daftar_karyawan.id', $this->id_karyawan)
                ->value('users.name');
        }

        $this->cartItems[] = [
            'id_produk'      => $this->id_produk,
            'nama_item'      => $this->nama_item,
            'kategori_item'  => $this->kategori_item,
            'deskripsi_item' => $this->deskripsi_item,
            'harga'          => $this->harga_item,
            'jumlah'         => $this->jumlah,
            'sub_total'      => $this->sub_total,
            'diskon'         => $this->diskon,
            'total_harga'    => $this->total_harga,
            'id_karyawan'    => $this->id_karyawan,
            'nama_karyawan'  => $this->nama_karyawan,
            'komisi_persen'  => $this->komisi_persen,
            'komisi_nominal' => $this->komisi_nominal,
        ];

        $this->dispatch('closeCart');
        $this->cancelCartItems();
        $this->getRingkasanTransaksi();
    }

    /* 
    | ======================================================
    |  <formDataModal> - Cart Items Logic and Ringkasan
    | ======================================================
    |
    |  deleteCartItems(): Menghapus item dari keranjang
    |     - Menghapus item berdasarkan index
    |     - Re-index array agar urutan tetap rapi (tanpa gap)
    |     - Update ringkasan total setelah penghapusan
    |
    |  getRingkasanTransaksi(): Hitung total dari semua item
    |     - Mengakumulasi jumlah item, sub_total, diskon,
    |       komisi, dan total akhir dari $cartItems
    |     - Digunakan setiap kali data keranjang berubah
    |
    | ======================================================
    */
    public function deleteCartItems($index)
    {
        unset($this->cartItems[$index]);                   // Hapus item di posisi tertentu
        $this->cartItems = array_values($this->cartItems); // Reindex array agar key berurutan
        $this->getRingkasanTransaksi();                    // Rehitung total ringkasan
        $this->dispatch('initSelect2');                   // Inisialisasi ulang Select2 jika diperlukan
    }

    public function getRingkasanTransaksi()
    {
        // Reset semua nilai ringkasan
        $total_pesanan   = 0;
        $total_sub_total = 0;
        $total_diskon    = 0;
        $total_akhir     = 0;
        $total_komisi    = 0;

        // Hitung total dari masing-masing item
        foreach ($this->cartItems as $item) {
            $total_pesanan      += $item['jumlah'] ?? 0;
            $total_sub_total    += $item['sub_total'] ?? 0;
            $total_diskon       += $item['diskon'] ?? 0;
            $total_komisi       += $item['komisi_nominal'] ?? 0;
            $total_akhir        += $item['total_harga'] ?? 0;
        }

        // Simpan hasil ke properti Livewire
        $this->total_pesanan   = $total_pesanan;
        $this->total_sub_total = $total_sub_total;
        $this->total_diskon    = $total_diskon;
        $this->total_komisi    = $total_komisi;
        $this->total_akhir     = $total_akhir;
    }
    // ---------------------- END formDataModal ------------------------

    /* 
    | ===================================================
    | <formPembayaranModal> - Pembayaran dan Kembalian
    | ===================================================
    |
    | formPembayaran():
    |     - Menghitung kembalian (dibayarkan - total akhir)
    |     - Dispatch event untuk inisialisasi ulang Select2
    |
    | uangPas():
    |     - Atur jumlah_dibayarkan sama dengan total_akhir
    |       (fitur 'uang pas' untuk pembayaran)
    |     - Inisialisasi ulang Select2
    |
    */
    public function formPembayaran()
    {
        $this->kembalian = $this->jumlah_dibayarkan - $this->total_akhir;
        $this->dispatch('initSelect2');
    }

    public function uangPas()
    {
        $this->jumlah_dibayarkan = $this->total_akhir;

        $this->initSelect2();
    }
    // ------------------ END formPembayaranModal Section ------------------

    private function resetInputFields()
    {
        $this->id_metode_pembayaran = $this->pembayarans->first()->id;
        $this->id_pelanggan         = $this->pelanggans->first()->id;

        $this->cartItems         = [];

        $this->catatan           = '-';
        $this->total_pesanan     = 0;
        $this->total_komisi      = 0;
        $this->total_sub_total   = 0;
        $this->total_diskon      = 0;
        $this->total_akhir       = 0;
        $this->laba_bersih       = 0;
        $this->jumlah_dibayarkan = 0;
        $this->kembalian         = 0;

        $this->nama_item         = '';
        $this->kategori_item     = '-';
        $this->deskripsi_item    = '-';
        $this->harga_item        = 0;
        $this->jumlah            = 1;
        $this->sub_total         = 0;
        $this->input_diskon      = 0;
        $this->diskon            = 0;
        $this->total_harga       = 0;
        $this->id_karyawan       = '';
        $this->nama_karyawan     = '-';
        $this->komisi_persen     = 0;
        $this->komisi_nominal    = 0;

        $this->initSelect2();
    }

    public function updated()
    {
        $this->dispatch('initSelect2');
    }

    public function cancel()
    {
        $this->resetInputFields();
    }

    public function cancelList()
    {
        $this->initSelect2();
    }

    public function initSelect2()
    {
        $this->dispatch('initSelect2');
    }

    public function generateNoTransaksi($id_cabang)
    {
        return DB::transaction(function () use ($id_cabang) {
            $tanggal = Carbon::now()->startOfDay();

            $counter = TransaksiCounter::where('id_cabang', $id_cabang)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                $counter = TransaksiCounter::create([
                    'id_cabang' => $id_cabang,
                    'tanggal' => $tanggal,
                    'nomor_terakhir' => 1,
                ]);
            } else {
                $counter->increment('nomor_terakhir');
            }

            $nomorUrut = str_pad($counter->nomor_terakhir, 5, '0', STR_PAD_LEFT);
            $tglFormat = $tanggal->format('dmy');

            return "TRX/{$id_cabang}/{$tglFormat}/{$nomorUrut}";
        });
    }

    public function resetFilter()
    {
        $this->filter_status     = '';
        $this->filter_pembayaran = '';
    }

    // public function updatedIsPersentase()
    // {
    //     // Saat user ganti jenis diskon, hitung ulang diskon & total
    //     if ($this->input_diskon > 0) {
    //         $this->updatedInputDiskon();
    //     } else {
    //         $this->diskon = 0;
    //         $this->total_harga = $this->sub_total;
    //     }
    // }
}
