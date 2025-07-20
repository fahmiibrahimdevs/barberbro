<?php

namespace App\Livewire\DataMaster;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Services\GlobalDataService;
use Illuminate\Support\Facades\Auth;
use App\Models\DaftarPelanggan as ModelsDaftarPelanggan;

class DaftarPelanggan extends Component
{
    use WithPagination;
    #[Title('Daftar Pelanggan')]

    protected $paginationTheme = 'bootstrap';
    protected $globalDataService;

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'id_user'             => 'required',
        'id_cabang'           => 'required',
        'nama_pelanggan'      => 'required',
        'no_telp'             => '',
        'deskripsi'           => '',
        'gambar'              => '',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    public $cabangs;
    public $id_user, $id_cabang, $nama_pelanggan, $no_telp, $deskripsi, $gambar;

    public function mount(GlobalDataService $globalDataService)
    {
        // Menyimpan instance dari service ke properti komponen
        $this->globalDataService = $globalDataService;

        // Ambil data global dari service
        $this->cabangs    = $this->globalDataService->getCabangs();

        $this->resetInputFields();
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = ModelsDaftarPelanggan::select('daftar_pelanggan.id', 'daftar_pelanggan.nama_pelanggan', 'daftar_pelanggan.no_telp', 'daftar_pelanggan.deskripsi', 'daftar_pelanggan.gambar', 'cabang_lokasi.nama_cabang', 'users.name')
            ->join('cabang_lokasi', 'cabang_lokasi.id', 'daftar_pelanggan.id_cabang')
            ->join('users', 'users.id', 'daftar_pelanggan.id_user')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', $search);
                $query->orWhere('nama_cabang', 'LIKE', $search);
                $query->orWhere('nama_pelanggan', 'LIKE', $search);
                $query->orWhere('daftar_pelanggan.no_telp', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.data-master.daftar-pelanggan', compact('data'));
    }

    public function store()
    {
        $this->validate();

        ModelsDaftarPelanggan::create([
            'id_user'             => $this->id_user,
            'id_cabang'           => $this->id_cabang,
            'nama_pelanggan'      => $this->nama_pelanggan,
            'no_telp'             => $this->no_telp,
            'deskripsi'           => $this->deskripsi,
            'gambar'              => $this->gambar,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit($id)
    {
        $this->isEditing        = true;
        $data = ModelsDaftarPelanggan::where('id', $id)->first();
        $this->dataId           = $id;
        $this->id_user          = $data->id_user;
        $this->id_cabang        = $data->id_cabang;
        $this->nama_pelanggan   = $data->nama_pelanggan;
        $this->no_telp          = $data->no_telp;
        $this->deskripsi        = $data->deskripsi;
        $this->gambar           = $data->gambar;

        $this->dispatch('initSelect2');
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            ModelsDaftarPelanggan::findOrFail($this->dataId)->update([
                'id_user'             => $this->id_user,
                'id_cabang'           => $this->id_cabang,
                'nama_pelanggan'      => $this->nama_pelanggan,
                'no_telp'             => $this->no_telp,
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
        ModelsDaftarPelanggan::findOrFail($this->dataId)->delete();
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
        $this->nama_pelanggan      = '';
        $this->no_telp             = '62';
        $this->deskripsi           = '-';
        $this->gambar              = '-';
    }

    public function cancel()
    {
        $this->isEditing       = false;
        $this->resetInputFields();
    }
}
