<?php

namespace App\Livewire\DataPendukung;

use App\Models\CabangLokasi as ModelsCabangLokasi;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class CabangLokasi extends Component
{
    use WithPagination;
    #[Title('Cabang Lokasi')]

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'nama_cabang'         => 'required',
        'alamat'              => '',
        'status'              => 'required',
        'no_telp'             => '',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;

    public $nama_cabang, $alamat, $status, $no_telp;

    public function mount()
    {
        $this->nama_cabang         = '';
        $this->alamat              = '';
        $this->status              = '';
        $this->no_telp             = '';
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = ModelsCabangLokasi::select('cabang_lokasi.*')
            ->where(function ($query) use ($search) {
                $query->where('nama_cabang', 'LIKE', $search);
                $query->orWhere('alamat', 'LIKE', $search);
                $query->orWhere('status', 'LIKE', $search);
                $query->orWhere('no_telp', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.data-pendukung.cabang-lokasi', compact('data'));
    }

    public function store()
    {
        $this->validate();

        ModelsCabangLokasi::create([
            'nama_cabang'         => $this->nama_cabang,
            'alamat'              => $this->alamat,
            'status'              => $this->status,
            'no_telp'             => $this->no_telp,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit($id)
    {
        $this->isEditing        = true;
        $data = ModelsCabangLokasi::where('id', $id)->first();
        $this->dataId           = $id;
        $this->nama_cabang      = $data->nama_cabang;
        $this->alamat           = $data->alamat;
        $this->status           = $data->status;
        $this->no_telp          = $data->no_telp;
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            ModelsCabangLokasi::findOrFail($this->dataId)->update([
                'nama_cabang'         => $this->nama_cabang,
                'alamat'              => $this->alamat,
                'status'              => $this->status,
                'no_telp'             => $this->no_telp,
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
        ModelsCabangLokasi::findOrFail($this->dataId)->delete();
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
    }

    private function resetInputFields()
    {
        $this->nama_cabang         = '';
        $this->alamat              = '';
        $this->status              = '';
        $this->no_telp             = '';
    }

    public function cancel()
    {
        $this->isEditing       = false;
        $this->resetInputFields();
    }
}
