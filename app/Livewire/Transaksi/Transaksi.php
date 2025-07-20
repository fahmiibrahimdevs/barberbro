<?php

namespace App\Livewire\Transaksi;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use App\Services\GlobalDataService;
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
        'no_transaksi' => 'required',
    ];

    public $lengthData = 25;
    public $searchTerm, $searchProduk;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    public $cabangs, $pelanggans, $karyawans, $produks;
    public $isPersentase = false;
    public $input_diskon;
    public $id_cabang, $no_transaksi, $tanggal, $id_pelanggan, $id_karyawan, $total_komisi_karyawan, $sub_total, $total_akhir, $id_metode_pembayaran, $dibayar, $kembalian, $catatan;

    public $id_transaksi, $id_produk, $jumlah, $harga, $diskon, $total_harga, $komisi;

    public function mount(GlobalDataService $globalDataService)
    {
        $this->globalDataService = $globalDataService;
        // $this->cabangs           = $this->globalDataService->getCabangs();
        $this->pelanggans        = $this->globalDataService->getPelanggans();
        $this->karyawans         = $this->globalDataService->getKaryawans();
        $this->produks           = $this->globalDataService->getProdukAndKategori();

        $this->resetInputFields();
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = ModelsTransaksi::select('transaksi.*', 'daftar_pelanggan.nama_pelanggan')
            ->join('daftar_pelanggan', 'daftar_pelanggan.id', 'transaksi.id_pelanggan')
            ->where(function ($query) use ($search) {
                $query->where('no_transaksi', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.transaksi.transaksi', compact('data'));
    }

    public function store()
    {
        $this->validate();

        ModelsTransaksi::create([
            'id_cabang'             => $this->id_cabang,
            'no_transaksi'          => $this->no_transaksi,
            'tanggal'               => $this->tanggal,
            'id_pelanggan'          => $this->id_pelanggan,
            'id_karyawan'           => $this->id_karyawan,
            'total_komisi_karyawan' => $this->total_komisi_karyawan,
            'sub_total'             => $this->sub_total,
            'total_akhir'           => $this->total_akhir,
            'id_metode_pembayaran'  => $this->id_metode_pembayaran,
            'dibayar'               => $this->dibayar,
            'kembalian'             => $this->kembalian,
            'catatan'               => $this->catatan,
        ]);

        DetailTransaksi::create([
            'id_transaksi' => $this->id_transaksi,
            'id_produk'    => $this->id_produk,
            'jumlah'       => $this->jumlah,
            'harga'        => $this->harga,
            'diskon'       => $this->diskon,
            'total_harga'  => $this->total_harga,
            'komisi'       => $this->komisi,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit($id)
    {
        $this->isEditing = true;
        $data = ModelsTransaksi::findOrFail($id);
        $this->dataId = $id;
        $this->no_transaksi  = $data->no_transaksi;

        $this->dispatch('initSelect2');
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            ModelsTransaksi::create([
                'id_cabang'             => $this->id_cabang,
                'no_transaksi'          => $this->no_transaksi,
                'tanggal'               => $this->tanggal,
                'id_pelanggan'          => $this->id_pelanggan,
                'id_karyawan'           => $this->id_karyawan,
                'total_komisi_karyawan' => $this->total_komisi_karyawan,
                'sub_total'             => $this->sub_total,
                'total_akhir'           => $this->total_akhir,
                'id_metode_pembayaran'  => $this->id_metode_pembayaran,
                'dibayar'               => $this->dibayar,
                'kembalian'             => $this->kembalian,
                'catatan'               => $this->catatan,
            ]);

            DetailTransaksi::create([
                'id_transaksi' => $this->id_transaksi,
                'id_produk'    => $this->id_produk,
                'jumlah'       => $this->jumlah,
                'harga'        => $this->harga,
                'diskon'       => $this->diskon,
                'total_harga'  => $this->total_harga,
                'komisi'       => $this->komisi,
            ]);

            $this->dispatchAlert('success', 'Success!', 'Data updated successfully.');
            $this->dataId = null;
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

    public function updatedSearchProduk()
    {
        $search = '%' . $this->searchProduk . '%';

        $this->produks = DB::table('produk')->select('produk.id', 'nama_item', 'harga_jasa', 'nama_kategori', 'produk.deskripsi')
            ->join('kategori_produk', 'kategori_produk.id', 'produk.id_kategori')
            ->where(function ($query) use ($search) {
                $query->where('nama_item', 'LIKE', $search);
                $query->orWhere('produk.deskripsi', 'LIKE', $search);
            })
            ->get();
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

    public function isEditingMode($mode)
    {
        $this->isEditing = $mode;
        $this->dispatch('initSelect2');
    }

    public function initSelectTwo()
    {
        $this->dispatch('initSelect2');
    }

    private function resetInputFields()
    {
        $this->no_transaksi = '';
        $this->jumlah       = 1;
    }

    public function cancel()
    {
        $this->resetInputFields();
    }

    public function incrementJumlah()
    {
        $this->jumlah++;
    }

    public function decrementJumlah()
    {
        $this->jumlah--;
    }
}
