<?php

namespace App\Livewire\DataMaster;

use Livewire\Component;
use App\Models\CabangLokasi;
use Livewire\WithPagination;
use App\Models\KategoriProduk;
use App\Models\KategoriSatuan;
use Livewire\Attributes\Title;
use App\Services\GlobalDataService;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk as ModelsProduk;

class Produk extends Component
{
    use WithPagination;
    #[Title('Produk')]

    protected $paginationTheme = 'bootstrap';
    protected $globalDataService;

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'id_cabang'           => 'required',
        'id_user'             => 'required',
        'id_kategori'         => 'required',
        'id_satuan'           => 'required',
        'kode_item'           => '',
        'nama_item'           => 'required',
        'harga_jasa'          => '',
        'harga_pokok'         => '',
        'harga_jual'          => '',
        'stock'               => '',
        'deskripsi'           => '',
        'gambar'              => '',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    public $cabangs, $kategoris, $satuans;
    public $id_cabang, $id_user, $id_kategori, $id_satuan, $kode_item, $nama_item, $harga_jasa, $harga_pokok, $harga_jual, $stock, $deskripsi, $gambar;

    // Menggunakan mount untuk inject service
    public function mount(GlobalDataService $globalDataService)
    {
        // Menyimpan instance dari service ke properti komponen
        $this->globalDataService = $globalDataService;

        // Ambil data global dari service
        $this->cabangs    = $this->globalDataService->getCabangs();
        $this->kategoris  = $this->globalDataService->getKategoris();
        $this->satuans    = $this->globalDataService->getSatuans();

        $this->resetInputFields();
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = ModelsProduk::select('produk.id', 'produk.nama_item', 'produk.harga_jasa', 'produk.stock', 'produk.deskripsi', 'produk.gambar', 'cabang_lokasi.nama_cabang', 'kategori_produk.nama_kategori', 'kategori_satuan.nama_satuan')
            ->leftJoin('cabang_lokasi', 'cabang_lokasi.id', 'produk.id_cabang')
            ->leftJoin('kategori_produk', 'kategori_produk.id', 'produk.id_kategori')
            ->leftJoin('kategori_satuan', 'kategori_satuan.id', 'produk.id_satuan')
            ->where(function ($query) use ($search) {
                $query->where('id_cabang', 'LIKE', $search);
                $query->orWhere('nama_kategori', 'LIKE', $search);
                $query->orWhere('kode_item', 'LIKE', $search);
                $query->orWhere('nama_item', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.data-master.produk', compact('data'));
    }

    public function store()
    {
        $this->validate();

        ModelsProduk::create([
            'id_cabang'           => $this->id_cabang,
            'id_user'             => $this->id_user,
            'id_kategori'         => $this->id_kategori,
            'id_satuan'           => $this->id_satuan,
            'kode_item'           => $this->kode_item,
            'nama_item'           => $this->nama_item,
            'harga_jasa'          => $this->harga_jasa,
            'harga_pokok'         => $this->harga_pokok,
            'harga_jual'          => $this->harga_jual,
            'stock'               => $this->stock,
            'deskripsi'           => $this->deskripsi,
            'gambar'              => $this->gambar,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit($id)
    {
        $this->isEditing        = true;
        $this->dispatch('initSelect2');
        $data = ModelsProduk::where('id', $id)->first();
        $this->dataId           = $id;
        $this->id_cabang        = $data->id_cabang;
        $this->id_user          = $data->id_user;
        $this->id_kategori      = $data->id_kategori;
        $this->id_satuan        = $data->id_satuan;
        $this->kode_item        = $data->kode_item;
        $this->nama_item        = $data->nama_item;
        $this->harga_jasa       = $data->harga_jasa;
        $this->harga_pokok      = $data->harga_pokok;
        $this->harga_jual       = $data->harga_jual;
        $this->stock            = $data->stock;
        $this->deskripsi        = $data->deskripsi;
        $this->gambar           = $data->gambar;
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            ModelsProduk::findOrFail($this->dataId)->update([
                'id_cabang'           => $this->id_cabang,
                'id_user'             => Auth::user()->id,
                'id_kategori'         => $this->id_kategori,
                'id_satuan'           => $this->id_satuan,
                'kode_item'           => $this->kode_item,
                'nama_item'           => $this->nama_item,
                'harga_jasa'          => $this->harga_jasa,
                'harga_pokok'         => $this->harga_pokok,
                'harga_jual'          => $this->harga_jual,
                'stock'               => $this->stock,
                'deskripsi'           => $this->deskripsi,
                'gambar'              => $this->gambar,
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
        ModelsProduk::findOrFail($this->dataId)->delete();
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

    public function isEditingMode($mode)
    {
        $this->isEditing = $mode;
        $this->dispatch('initSelect2');
    }

    private function resetInputFields()
    {
        $this->id_user             = Auth::user()->id;
        $this->id_cabang           = $this->cabangs->first()->id;
        $this->id_kategori         = $this->kategoris->first()->id;
        $this->id_satuan           = $this->satuans->first()->id;
        $this->kode_item           = '';
        $this->nama_item           = '';
        $this->harga_jasa          = '0';
        $this->harga_pokok         = '0';
        $this->harga_jual          = '0';
        $this->stock               = '0';
        $this->deskripsi           = '-';
        $this->gambar              = '-';
    }

    public function cancel()
    {
        $this->isEditing       = false;
        $this->resetInputFields();
    }
}
